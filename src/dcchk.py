import requests,sys,time,lxml
from bs4 import BeautifulSoup

headers = { 'User-Agent': 'Mozilla/5.0 (Linux; Android 8.0; Pixel 2 Build/OPD3.170816.012) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.81 Mobile Safari/537.36' }

url = 'https://m.dcinside.com/board/' + 'euca' + '/' + sys.argv[1]
r = requests.get(url, headers = headers).text
bs = BeautifulSoup(r, 'lxml')

try:
    if (bs.find('span', class_='tit') == None):
        if ('게시물이 존재하지 않거나 삭제되었습니다' in bs.find('div', class_='container').text):
            state = 'DELETED'
        else:
            state = 'UNKNOWN'
    else:
        # 제목
        title = list(filter(None, bs.find('span', class_='tit').text.split('\r\n')))[1].strip()
        infos = bs.find_all('ul', class_='ginfo2')
        p = bs.find('div', class_='gall-thum-btm')
        info1 = list(filter(None, infos[1].text.split('\n')))
        views = int(info1[0].split(' ')[1])
        comments = int(info1[2].split(' ')[1])
        upvote = int(bs.find('span', class_='ct').text.strip())

        # 갤로그 찾기
        gallogbt = bs.find('a', class_='btn btn-line-gray')
        info0 = list(filter(None, infos[0].text.split('\n')))
        isgonic = not (gallogbt == None)
        # isrecom = (bs.find('button', class_='sp-icon sp-rega on') != None)
        # date_time = info0[1]

        if isgonic:
            # 고닉일 시
            ipid = gallogbt['href'][gallogbt['href'].rfind('/')+1:]
            nick = info0[0]
        else:
            # 유동일 시
            ipid = info0[0][info0[0].rfind('(')+1:info0[0].rfind(')')]
            nick = info0[0][:info0[0].rfind('(')]

        print('<title>'+title+'</title>')
        print('<nick>'+nick+'</nick>')
        state = 'EXISTS'
except:
    state = 'ERROR'

print('<state>'+state+'</state>')


    