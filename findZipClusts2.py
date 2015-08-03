from bs4 import BeautifulSoup
import requests
import sys

f = open('zip_clusters2.txt', 'w')

zipC = sys.argv[1]
r  = requests.get('http://www.searchbug.com/tools/zip-radius.aspx?afid=sbug&zipcode='+ zipC +'&radius=60&submit=Search')
data = r.text

soup = BeautifulSoup(data, "lxml")
list_of_a = soup.find_all('a')
i = 0
for a in list_of_a:
    if a.text.isnumeric():
        if i == 0:
            i = i + 1
        else:
            f.write(a.text+":")
f.close