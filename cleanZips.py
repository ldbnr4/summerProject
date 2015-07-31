from bs4 import BeautifulSoup
import sys
import urllib2

f = open('bad_zips.txt', 'a')

zipC = sys.argv[1]
r  = urllib2.urlopen('http://www.unitedstateszipcodes.org/'+zipC)
data = r.read()

soup = BeautifulSoup(data, "lxml")
#print (soup.get_text()).encode('utf8')
#print len(soup.find_all('td', 'info'))
'''for b in soup.find_all('td', 'lable'):
    if "Not Found" in b.text:
        f.write(zipC+ " ")
        print "Bad zip"
        print zipC'''
        
for a in soup.find_all('th'):
    if "PO Box" in a.text:
        f.write(zipC+ " ")
        print "PO BOX"
        print zipC
f.close