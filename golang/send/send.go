package main

import (
	"encoding/json"
	"fmt"
	"io"
	"log"
	"net/http"

	"github.com/streadway/amqp"
)

// Ret ...
type Ret struct {
	Code int    `json:"code,int"`
	Data string `json:"data"`
}

func failOnError(err error, msg string) {
	if err != nil {
		log.Fatalf("%s: %s", msg, err)
	}
}


func processOrder(r *http.Request) {
	msg, _ := json.Marshal(r.Form)

	conn, err := amqp.Dial("amqp://username:password@ip:port/")
	failOnError(err, "Failed to connect to RabbitMQ")

	ch, err := conn.Channel()
	failOnError(err, "Failed to open a channel")

	q, err := ch.QueueDeclare(
		"hello", // name
		false,   // durable
		false,   // delete when unused
		false,   // exclusive
		false,   // no-wait
		nil,     // arguments
	)
	failOnError(err, "Failed to declare a queue")

	err = ch.Publish(
		"",     // exchange
		q.Name, // routing key
		false,  // mandatory
		false,  // immediate
		amqp.Publishing{
			ContentType: "text/plain",
			Body:        msg,
		})
	log.Printf(" [x] Sent %s", msg)
	log.Printf("-----Submit order-----")
	failOnError(err, "Failed to publish a message")
	conn.Close()
	ch.Close()
}

func printRequest(w http.ResponseWriter, r *http.Request) {
	fmt.Println("r.Form = ", r.Form) //这些信息是输出到服务器端的打印信息 , Get参数

	ret := new(Ret)
	ret.Code = 200
	ret.Data = "提交成功"
	w.Header().Set("Content-Type", "application/json; charset=utf-8")
	retJSON, _ := json.Marshal(ret)
	io.WriteString(w, string(retJSON))
}

func handle(w http.ResponseWriter, r *http.Request) {
	log.Printf("-----Receive order-----")
	r.ParseForm()
	processOrder(r)
	printRequest(w, r)
	log.Printf("-----Wating for order-----")
}

func main() {
	log.Printf("-----System running-----")
	http.HandleFunc("/api/order", handle)    //设置访问的路径
	err := http.ListenAndServe(":5000", nil) //设置监听的端口
	if err != nil {
		log.Fatal("ListenAndServe: ", err)
	}
}
