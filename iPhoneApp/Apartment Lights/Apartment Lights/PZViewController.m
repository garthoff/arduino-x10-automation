//
//  PZViewController.m
//  Apartment Lights
//
//  Created by Eric Barch on 1/16/12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import "PZViewController.h"

@implementation PZViewController

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Release any cached data, images, etc that aren't in use.
}

#pragma mark - View lifecycle

- (void)viewDidLoad
{
    [super viewDidLoad];
	// Do any additional setup after loading the view, typically from a nib.
    socket = [[GCDAsyncUdpSocket alloc] initWithDelegate:self delegateQueue:dispatch_get_main_queue()];
}

- (void)viewDidUnload
{
    [super viewDidUnload];
    // Release any retained subviews of the main view.
    // e.g. self.myOutlet = nil;
}

- (void)viewWillAppear:(BOOL)animated
{
    [super viewWillAppear:animated];
}

- (void)viewDidAppear:(BOOL)animated
{
    [super viewDidAppear:animated];
}

- (void)viewWillDisappear:(BOOL)animated
{
	[super viewWillDisappear:animated];
}

- (void)viewDidDisappear:(BOOL)animated
{
	[super viewDidDisappear:animated];
}

- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation
{
    // Return YES for supported orientations
    return (interfaceOrientation != UIInterfaceOrientationPortraitUpsideDown);
}

- (IBAction)lrOff:(id)sender {
    const char lightCommand[] = { 0x01 };
    NSData *data = [NSData dataWithBytes:(const void *)lightCommand length:1];
    [socket sendData:data toHost:@"192.168.0.3" port:4444 withTimeout:-1 tag:1];
}

- (IBAction)lrOn:(id)sender {
    const char lightCommand[] = { 0x00 };
    NSData *data = [NSData dataWithBytes:(const void *)lightCommand length:1];
    [socket sendData:data toHost:@"192.168.0.3" port:4444 withTimeout:-1 tag:1];
}

- (IBAction)alexOn:(id)sender {
    const char lightCommand[] = { 0x04 };
    NSData *data = [NSData dataWithBytes:(const void *)lightCommand length:1];
    [socket sendData:data toHost:@"192.168.0.3" port:4444 withTimeout:-1 tag:1];
}

- (IBAction)alexOff:(id)sender {
    const char lightCommand[] = { 0x05 };
    NSData *data = [NSData dataWithBytes:(const void *)lightCommand length:1];
    [socket sendData:data toHost:@"192.168.0.3" port:4444 withTimeout:-1 tag:1];
}
@end
