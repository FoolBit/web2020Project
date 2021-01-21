package main

import (
	"encoding/json"
	"errors"
	"fmt"
	"log"
	"net/url"
	"strconv"

	"github.com/streadway/amqp"
	"gorm.io/driver/mysql"
	"gorm.io/gorm"
)

//Order ...
type Order struct {
	ID    int `gorm:"primaryKey"`
	Gid   int `gorm:"column:good_id"`
	Uid   int `gorm:"column:user_id"`
	State int `gorm:"column:state"`
}

// Good ...
type Good struct {
	ID     int `gorm:"primaryKey"`
	Amount int `gorm:"column:amount"` // 楼id
	Sold   int `gorm:"column:sold"`
}

func secKill(msg url.Values) {
	gid, _ := strconv.Atoi(msg["gid"][0])
	uid, _ := strconv.Atoi(msg["uid"][0])
	num, _ := strconv.Atoi(msg["num"][0])

	//连接数据库
	dsn := "username:password@tcp(ip:port)/market?charset=utf8mb4&parseTime=True&loc=Local&timeout=20s"
	db, err := gorm.Open(mysql.Open(dsn), &gorm.Config{})
	if err != nil {
		fmt.Println(err)
	}
	fmt.Println("------------连接数据库成功-----------")
	isok := 0

	// 取出楼ID
	good := Good{}
	errGood := db.Table("market_good").Where(" id = ?", gid).First(&good).Error
	if errors.Is(errGood, gorm.ErrRecordNotFound) {
		fmt.Println("没有该商品")
	} else {
		isok = 1
		fmt.Println(good.Amount)
	}

	if good.Amount == good.Sold {
		isok = 0
	}

	remain := good.Amount - good.Sold
	if remain < num {
		isok = 0
	}
	// 开始事务

	tx := db.Begin()

	if isok == 1 {

		order := Order{Uid: uid, Gid: gid, State: 1}
		db.Table("market_order").Create(&order)
		fmt.Println("订单写入成功!")
		db.Table("market_good").Model(&good).Update("sold", good.Sold+num)
		fmt.Println("商品更新成功!")
		tx.Rollback()
	} else {
		// 如果未找到
		fmt.Println("购买失败")
		order := Order{Uid: uid, Gid: gid, State: 2}
		db.Table("market_order").Create(&order)
	}

	// 否则，提交事务
	tx.Commit()
}

func failOnError(err error, msg string) {
	if err != nil {
		log.Fatalf("%s: %s", msg, err)
	}
}

func main() {
	conn, err := amqp.Dial("amqp://username:password@ip:port/")
	failOnError(err, "Failed to connect to RabbitMQ")
	defer conn.Close()

	ch, err := conn.Channel()
	failOnError(err, "Failed to open a channel")
	defer ch.Close()

	q, err := ch.QueueDeclare(
		"hello", // name
		false,   // durable
		false,   // delete when unused
		false,   // exclusive
		false,   // no-wait
		nil,     // arguments
	)
	failOnError(err, "Failed to declare a queue")

	msgs, err := ch.Consume(
		q.Name, // queue
		"",     // consumer
		true,   // auto-ack
		false,  // exclusive
		false,  // no-local
		false,  // no-wait
		nil,    // args
	)
	failOnError(err, "Failed to register a consumer")

	forever := make(chan bool)

	go func() {
		for d := range msgs {
			msg := new(url.Values)
			err := json.Unmarshal(d.Body, &msg)
			if err != nil {
				fmt.Println(err)
			}
			log.Printf("Received a message: %s", msg)
			secKill(*msg)
		}
	}()

	log.Printf(" [*] Waiting for messages. To exit press CTRL+C")
	<-forever
}
