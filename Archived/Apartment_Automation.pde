#include <SPI.h>
#include <Ethernet.h>
#include <x10.h>
#include <x10constants.h>

#define zcPin 2  //GREEN END
#define dataPin 3  //RED END
#define repeatTimes 1   // how many times each X10 message should repeat

// set up a new x10 instance:
x10 myHouse = x10(zcPin, dataPin);

// Enter a MAC address and IP address for your controller below.
// The IP address will be dependent on your local network:
byte mac[] = { 0xDE, 0xAD, 0xBE, 0xEF, 0xC0, 0xFE };
byte ip[] = { 10, 4, 4, 244 };

// Initialize the Ethernet server library
// with the IP address and port you want to use 
// (port 80 is default for HTTP):
Server server(80);

void setup()
{
  digitalWrite(zcPin, HIGH);
  // start the Ethernet connection and the server:
  Ethernet.begin(mac, ip);
  server.begin();
}

void loop()
{
  // listen for incoming clients
  Client client = server.available();
  if (client) {
    // an http request ends with a blank line
    boolean currentLineIsBlank = true;
    boolean recvRequest = false;
    boolean firstslash = false;
    boolean secondspace = false;
    int recvIndex = 0;
    char getRequest[60];
    
    while (client.connected()) {
      if (client.available()) {
        char c = client.read();
        if (recvRequest == false) {
          if (c == '\n' || recvIndex >= 50)
            recvRequest = true;
          else if (c == '/' && firstslash == false)
            firstslash = true;
          else if (c == ' ' && firstslash == true && secondspace == false)
            secondspace = true;
          else if (firstslash == true && secondspace == false)
            getRequest[recvIndex++] = c;
        }
        // if you've gotten to the end of the line (received a newline
        // character) and the line is blank, the http request has ended,
        // so you can send a reply
        if (c == '\n' && currentLineIsBlank) {
          // send a standard http response header
          client.println("HTTP/1.1 200 OK");
          client.println("Content-Type: text/html");
          client.println();
          
          if (strcmp(getRequest, "lroff") == 0) {
            myHouse.write(A,UNIT_1,repeatTimes);
            myHouse.write(A,DIM,19);
            myHouse.write(A,ALL_LIGHTS_OFF,repeatTimes);
          }
          else if (strcmp(getRequest, "lron") == 0) {
            myHouse.write(A,UNIT_1,repeatTimes);
            myHouse.write(A,BRIGHT,19);
            myHouse.write(A,ALL_LIGHTS_ON,repeatTimes);
          }
          else if (strcmp(getRequest, "aoff") == 0) {
            myHouse.write(B,UNIT_1,repeatTimes);
            myHouse.write(B,DIM,19);
            myHouse.write(B,ALL_LIGHTS_OFF,repeatTimes);
          }
          else if (strcmp(getRequest, "aon") == 0) {
            myHouse.write(B,UNIT_1,repeatTimes);
            myHouse.write(B,BRIGHT,19);
            myHouse.write(B,ALL_LIGHTS_ON,repeatTimes);
          }
          else if (strcmp(getRequest, "coff") == 0) {
            myHouse.write(C,UNIT_1,repeatTimes);
            myHouse.write(C,DIM,19);
            myHouse.write(C,ALL_LIGHTS_OFF,repeatTimes);
          }
          else if (strcmp(getRequest, "con") == 0) {
            myHouse.write(C,UNIT_1,repeatTimes);
            myHouse.write(C,BRIGHT,19);
            myHouse.write(C,ALL_LIGHTS_ON,repeatTimes);
          }
          else if (strcmp(getRequest, "eoff") == 0) {
            myHouse.write(D,UNIT_1,repeatTimes);
            myHouse.write(D,DIM,19);
            myHouse.write(D,ALL_LIGHTS_OFF,repeatTimes);
          }
          else if (strcmp(getRequest, "eon") == 0) {
            myHouse.write(D,UNIT_1,repeatTimes);
            myHouse.write(D,BRIGHT,19);
            myHouse.write(D,ALL_LIGHTS_ON,repeatTimes);
          }
          
          client.print(header());
          client.print(lroom());
          client.print(alex());
          client.print(chass());
          client.print(eric());

          break;
        }
        if (c == '\n') {
          // you're starting a new line
          currentLineIsBlank = true;
        } 
        else if (c != '\r') {
          // you've gotten a character on the current line
          currentLineIsBlank = false;
        }
      }
    }
    // give the web browser time to receive the data
    delay(1);
    // close the connection:
    client.stop();
  }
}

char* header() {
   return "<h1>Apt Lighting</h1><br />";
}

char* lroom() {
   return "<h2>Living Room</h2><h1><a href=\"http://10.4.4.244/lroff\">OFF</a> | <a href=\"http://10.4.4.244/lron\">ON</a></h1>";
}

char* alex() {
   return "<h2>Alex's Room</h2><h1><a href=\"http://10.4.4.244/aoff\">OFF</a> | <a href=\"http://10.4.4.244/aon\">ON</a></h1>";
}

char* chass() {
   return "<h2>Chass' Room</h2><h1><a href=\"http://10.4.4.244/coff\">OFF</a> | <a href=\"http://10.4.4.244/con\">ON</a></h1>";
}

char* eric() {
   return "<h2>Eric's Room</h2><h1><a href=\"http://10.4.4.244/eoff\">OFF</a> | <a href=\"http://10.4.4.244/eon\">ON</a></h1>";
}
