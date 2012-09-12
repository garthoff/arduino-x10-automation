//
//  PZViewController.h
//  Apartment Lights
//
//  Created by Eric Barch on 1/16/12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "GCDAsyncUdpSocket.h"



@interface PZViewController : UIViewController {
GCDAsyncUdpSocket *socket;
}
- (IBAction)lrOff:(id)sender;
- (IBAction)lrOn:(id)sender;
- (IBAction)alexOn:(id)sender;
- (IBAction)alexOff:(id)sender;
@end
