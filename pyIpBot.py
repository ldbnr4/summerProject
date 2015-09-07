from bs4 import BeautifulSoup
import requests


def getLocation (ip) :
    page = requests.get('http://whatismyipaddress.com/ip/'+ip)
    if page.status_code is not 200:
        print('NULL')
        return
    soup = BeautifulSoup(page.text, "lxml")
    list_of_th = soup.find_all('th')
    for th in list_of_th:
        header = th.text
        if header == "State/Region:":
            print th.next_sibling.text
        elif header == "City:":
            print th.next_sibling.text
        elif header == "Postal Code:":
            print th.next_sibling.text