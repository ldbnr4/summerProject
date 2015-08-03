from bs4 import BeautifulSoup
import mechanize
import sys
import urllib2

f = open('no_good_zips.txt', 'a')
zipC = sys.argv[1]
br = mechanize.Browser()
br.set_handle_robots(False)   # ignore robots
response = br.open('http://www.searchbug.com/tools/zip-code-maps.aspx')

br.select_form("byzip")         # works when form has a name

br["zip"] = zipC
response = br.submit()
data = response.read()

soup = BeautifulSoup(data, "lxml")

for a in soup.find_all('center'):
    if "Boxes only!" in a.text:
        f.write(zipC+" ")
        print zipC+" is bad"
    if "for a single organization only!" in a.text:
        f.write(zipC+" ")
        print zipC+" is bad"
f.close