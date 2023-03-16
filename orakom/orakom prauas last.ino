//Include Library Start
#include "DHT.h"                                                                                                  //sensor dht11 
#ifdef ESP32
 #include <WiFi.h>
#include <HTTPClient.h>
#else
 #include <ESP8266WiFi.h>
 #include <ESP8266HTTPClient.h>
 #include <WiFiClient.h>
#endif
#include <Arduino_JSON.h>
//Include Library End

//Define pin Start
#define DHT11PIN 32
#define Sensor_input 33
#define relay1 4
#define relay2 0
#define buzz 27 
//Define pin End

//Define Perlengkapan connect Start
#define WLAN_SSID       "HEHE"
#define WLAN_PASS       "11223344"

//Define Perlengkapan connect End                                        

//Define Variable Start
 String Status_Lampu;
 String Status_Fan;
 String Status_Gas;

  int Lampu = HIGH;
  int Fan = HIGH;

  int off = HIGH;

 bool TampilkanTombol;

 float temp;
 float humi;

 int sensor;
 float sensorA;

 String Data;
 String gas;

 String status_gas;
 String postData = "";                                                       //Variable untuk mengirim HTTP POST request data.
 String payload = "";                                                        //Variable untuk menerima response dari HTTP POST.


  String tombol1_State = "";
  String tombol2_State = "";
//Define Variable End

DHT dht(DHT11PIN, DHT11);                                                   //Define pin DHT11

void setup()
{

  Serial.begin(115200);
  pinMode(Sensor_input, INPUT);
  pinMode(relay1 , OUTPUT);
  pinMode(relay2 , OUTPUT);
  pinMode(buzz , OUTPUT);
  digitalWrite(relay1,HIGH);  
  digitalWrite(relay2,HIGH);  


  // Connect to WiFi 
 Serial.println(); Serial.println();
 Serial.print("Connecting to ");
 Serial.println(WLAN_SSID);

 WiFi.begin(WLAN_SSID, WLAN_PASS);
 while (WiFi.status() != WL_CONNECTED) {
   delay(500);
   Serial.print(".");
 }
 Serial.println();
 Serial.println("WiFi connected");
 Serial.println("IP address: "); Serial.println(WiFi.localIP());

 

/* memulai dht */
  dht.begin();

}

uint32_t x=0;

void loop()
{
  //Membaca Data Humiditiy dan Temperature Start
  humi = dht.readHumidity();
  temp = dht.readTemperature();
  // Serial.print("Temperature: ");
  // Serial.print(temp);
  // Serial.print("ºC ");
  // Serial.print("Humidity: ");
  // Serial.println(humi);
  //Membaca Data Humiditiy dan Temperature End

  //Membaca Data Gas Start
  sensorA = analogRead(Sensor_input);   //membaca data analog 
  // sensor = digitalRead(Sensor_input);   //membaca data digital berupa 1 atau 0
  // Serial.print("gas : ");
  // Serial.println(sensor);
  // Serial.print("kadar gas : ");  
  // Serial.print(sensorA);   
  // Serial.print("\t");
  // Serial.print("\t");
  //Membaca Data Gas End

  if (sensorA > 1100) {    //if condition with threshold 1800/
  gas = "DANGER";
  digitalWrite(relay1,HIGH);
  digitalWrite(relay2,HIGH);
   tone(buzz, 1000); 
   delay(1000);
   noTone(buzz); 
   delay(1000);
  }else{
  gas = "SAFE";
  ambilkirim();
  noTone(buzz); 
  delay(1500);
  }
}

void ambilkirim(){
    //Proses untuk mendapatkan data dari database Start
  postData = "id=esp32_01";
  
  Serial.println();
  Serial.println("---------------getdata.php");
  
  HTTPClient http;
  http.begin("https://orakompra.000webhostapp.com/getdata.php");  //--> Specify request destination
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");        //--> Specify content-type header
   
 
  auto httpCode = http.POST(postData);
  payload = http.getString();

  Serial.print("httpCode : ");
  Serial.println(httpCode); //--> Print HTTP return code
  Serial.print("payload  : ");
  Serial.println(payload);  //--> Print request response payload
    
  http.end();  //--> Close connection
  Serial.println("---------------");
  //Proses untuk mendapatkan data dari database End

  relay();                                                                                                   //Memanggil Void Relay   
  //
  if (Lampu == LOW) tombol1_State = "ON";
  if (Lampu == HIGH) tombol1_State = "OFF";
  if (Fan == LOW) tombol2_State = "ON";
  if (Fan == HIGH) tombol2_State = "OFF";
  postData = "id=esp32_01";
  postData += "&suhu=" + String(temp);
  postData += "&kelem=" + String(humi);
  postData += "&gas=" + String(sensorA);
  postData += "&status_gas=" + String(gas);
  postData += "&tombol1=" + String(tombol1_State);
  postData += "&tombol2=" + String(tombol2_State);
  payload = "";


  Serial.println();
  Serial.println("---------------updaterecordtable.php");
  http.begin("https://orakompra.000webhostapp.com/updaterecordtable.php");  //--> Specify request destination
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");  //--> Specify content-type header
   
  httpCode = http.POST(postData); //--> Send the request
  payload = http.getString();  //--> Get the response payload
  
  Serial.print("httpCode : ");
  Serial.println(httpCode); //--> Print HTTP return code
  Serial.print("payload  : ");
  Serial.println(payload);  //--> Print request response payload
    
  http.end();  //Close connection
  Serial.println("---------------");
  //Proses Mengirim Data ke Database End


}

void relay(){
  Serial.println();
  Serial.println("---------------tombol_relay()");
  JSONVar myObject = JSON.parse(payload);
  
  // JSON.typeof(jsonVar) can be used to get the type of the var
  if (JSON.typeof(myObject) == "undefined") {
    Serial.println("Parsing input failed!");
    Serial.println("---------------");
    return;
  }

  if (myObject.hasOwnProperty("tombol1")) {
    Serial.print("myObject[\"tombol1\"] = ");
    Serial.println(myObject["tombol1"]);
  }

  if (myObject.hasOwnProperty("tombol2")) {
    Serial.print("myObject[\"tombol2\"] = ");
    Serial.println(myObject["tombol2"]);
  }

  if(strcmp(myObject["tombol1"], "ON") == 0)   {digitalWrite(relay1, LOW);  Serial.println("LAMP ON"); Lampu = LOW; }
  if(strcmp(myObject["tombol1"], "OFF") == 0)  {digitalWrite(relay1, HIGH);    Serial.println("LAMP OFF"); Lampu = HIGH;}
  if(strcmp(myObject["tombol2"], "ON") == 0)   {digitalWrite(relay2, LOW);  Serial.println("FAN ON");Fan = LOW;}
  if(strcmp(myObject["tombol2"], "OFF") == 0)  {digitalWrite(relay2, HIGH);   Serial.println("FAN OFF");Fan = HIGH;}

  Serial.println("---------------");

  }