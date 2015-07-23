import datetime
from dateutil.relativedelta import relativedelta

sDate = datetime.date.today().strftime("%m/%d/%Y")
eDate =  datetime.date.today() + relativedelta(months=3)
eDate = eDate.strftime("%m/%d/%Y")

print sDate + ':' + eDate