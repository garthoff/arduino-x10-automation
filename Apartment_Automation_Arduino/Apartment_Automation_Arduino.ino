#include <SPI.h>        
#include <Ethernet.h>
#include <EthernetUdp.h>
#include <x10.h>
#include <x10constants.h>

byte mac[] = { 0xDE, 0xAD, 0xBE, 0xEF, 0xFE, 0xED };
IPAddress ip(192, 168, 0, 3);
unsigned int localPort = 4444;      // local port to listen on

#define zcPin 2  //GREEN END
#define dataPin 3  //RED END
#define repeatTimes 1   // how many times each X10 message should repeat

// set up a new x10 instance:
x10 myHouse = x10(zcPin, dataPin);

// An EthernetUDP instance to let us send and receive packets over UDP
EthernetUDP Udp;

char packetBuffer[UDP_TX_PACKET_MAX_SIZE]; //buffer to hold incoming packet

void setup()
{
  digitalWrite(zcPin, HIGH);
  
  // start the Ethernet and UDP:
  Ethernet.begin(mac,ip);
  Udp.begin(localPort);
}

void loop()
{
  
  
  int packetSize = Udp.parsePacket();
  if(packetSize)
  {
    // read the packet into packetBufffer
    Udp.read(packetBuffer,UDP_TX_PACKET_MAX_SIZE);
    
    if (packetBuffer[0] == (byte)1) {
            myHouse.write(A,UNIT_1,repeatTimes);
            myHouse.write(A,DIM,19);
            myHouse.write(A,ALL_LIGHTS_OFF,repeatTimes);
          }
          else if (packetBuffer[0] == (byte)0) {
            myHouse.write(A,UNIT_1,repeatTimes);
            myHouse.write(A,BRIGHT,19);
            myHouse.write(A,ALL_LIGHTS_ON,repeatTimes);
          }
          else if (packetBuffer[0] == (byte)5) {
            myHouse.write(B,UNIT_1,repeatTimes);
            myHouse.write(B,DIM,19);
            myHouse.write(B,ALL_LIGHTS_OFF,repeatTimes);
          }
          else if (packetBuffer[0] == (byte)4) {
            myHouse.write(B,UNIT_1,repeatTimes);
            myHouse.write(B,BRIGHT,19);
            myHouse.write(B,ALL_LIGHTS_ON,repeatTimes);
          }
          else if (packetBuffer[0] == (byte)7) {
            myHouse.write(C,UNIT_1,repeatTimes);
            myHouse.write(C,DIM,19);
            myHouse.write(C,ALL_LIGHTS_OFF,repeatTimes);
          }
          else if (packetBuffer[0] == (byte)6) {
            myHouse.write(C,UNIT_1,repeatTimes);
            myHouse.write(C,BRIGHT,19);
            myHouse.write(C,ALL_LIGHTS_ON,repeatTimes);
          }
          else if (packetBuffer[0] == (byte)3) {
            myHouse.write(D,UNIT_1,repeatTimes);
            myHouse.write(D,DIM,19);
            myHouse.write(D,ALL_LIGHTS_OFF,repeatTimes);
          }
          else if (packetBuffer[0] == (byte)2) {
            myHouse.write(D,UNIT_1,repeatTimes);
            myHouse.write(D,BRIGHT,19);
            myHouse.write(D,ALL_LIGHTS_ON,repeatTimes);
          }
  }
  
  
}
