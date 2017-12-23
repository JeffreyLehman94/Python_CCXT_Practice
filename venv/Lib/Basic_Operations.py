# coding=utf-8

import asyncio
import ccxt
import time
import _thread
import datetime

#Basic math and String creation
x = sum([1, 0])
if x == 1:
    print("0 + 1 = %s" % (x))
y = 2
if sum([x, y]) == 3:
    z = sum([x, y])
    print("This should count up: %s,  %s,  %s" % (x, y, z))

#Opens or creates a file named "abc"
file = open("abc", "w")
#Gets the current date/time
date = datetime.datetime.now()
#Write to the open document
file.write("%s     %s \n" % (date, x))
file.write("%s     %s \n" % (date, y))
file.write("%s     %s \n" % (date, z))
#Saves the written data to the document, then closes it
file.flush()
file.close()