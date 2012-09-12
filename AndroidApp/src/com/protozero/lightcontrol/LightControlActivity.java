package com.protozero.lightcontrol;

import java.net.DatagramPacket;
import java.net.DatagramSocket;
import java.net.InetAddress;

import android.app.Activity;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;

public class LightControlActivity extends Activity {
    /** Called when the activity is first created. */
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.main);
        
        final Button button = (Button) findViewById(R.id.lon);
        button.setOnClickListener(new View.OnClickListener() {
            public void onClick(View v) {
                sendByte(0);
            }
        });
        
        final Button button2 = (Button) findViewById(R.id.loff);
        button2.setOnClickListener(new View.OnClickListener() {
            public void onClick(View v) {
            	sendByte(1);
            }
        });
        
        final Button button3 = (Button) findViewById(R.id.eon);
        button3.setOnClickListener(new View.OnClickListener() {
            public void onClick(View v) {
            	sendByte(2);
            }
        });
        
        final Button button4 = (Button) findViewById(R.id.eoff);
        button4.setOnClickListener(new View.OnClickListener() {
            public void onClick(View v) {
            	sendByte(3);
            }
        });
        
        final Button button5 = (Button) findViewById(R.id.aon);
        button5.setOnClickListener(new View.OnClickListener() {
            public void onClick(View v) {
            	sendByte(4);
            }
        });
        
        final Button button6 = (Button) findViewById(R.id.aoff);
        button6.setOnClickListener(new View.OnClickListener() {
            public void onClick(View v) {
            	sendByte(5);
            }
        });
        
        final Button button7 = (Button) findViewById(R.id.ton);
        button7.setOnClickListener(new View.OnClickListener() {
            public void onClick(View v) {
            	sendByte(6);
            }
        });
        
        final Button button8 = (Button) findViewById(R.id.toff);
        button8.setOnClickListener(new View.OnClickListener() {
            public void onClick(View v) {
            	sendByte(7);
            }
        });
    }
    
    private void sendByte(final int val) {
    	new Thread(new Runnable() {
    	    public void run() {
    	    	try {
    		        DatagramSocket udpSocket = new DatagramSocket();
    		        byte auxByte = (byte) val;
    		        byte[] buf = new byte[] { auxByte };
    		        DatagramPacket p = new DatagramPacket(buf, buf.length, InetAddress.getByName("192.168.0.3"), 4444);
    		        udpSocket.send(p);
    	        }
    	        catch (Exception e) {}
    	    }
    	  }).start();
    }
}