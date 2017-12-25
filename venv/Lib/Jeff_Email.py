import ccxt
import smtplib
import time
import datetime

# Small script to email Jeff and Taylor if bat goes to the moon

def sendEmail(message, phone):
    smtpObj = smtplib.SMTP('smtp.gmail.com', 587)
    smtpObj.ehlo()
    smtpObj.starttls()
    smtpObj.login('jeffreylehman94@gmail.com', "Jeffreylehman1994")
    smtpObj.sendmail('jeffreylehman94@gmail.com', phone, message)
def getBinance():
    print("Start binance")
    symbol = 'BAT/ETH'
    exchange = ccxt.binance()
    markets = exchange.load_markets()
    ticker = exchange.fetch_ticker(symbol.upper())
    print(ticker)
    price=ticker['bid']
    print(price)
    if price>0.000817485:
        sendEmail("BAT IS GOING TO THE MOON", '6094206366@txt.att.net')
        sendEmail("BAT IS GOING TO THE MOON", '8569797926@vtext.com')
        print("PRICE OVER +50%")
    print("IT RAN")
    print(str(datetime.datetime.now()))
    return price
while True:
    getBinance()
    time.sleep(600)