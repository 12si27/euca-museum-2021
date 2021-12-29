# euca-museum-2021
![image](https://user-images.githubusercontent.com/88251502/141685076-8db2ac81-9cac-4860-b642-228feafb2131.png)

Bootstrap 5 + PHP 기반 갤러리 보존 열람 사이트  
<sub>\* gallreader를 통해 수집한 글과 댓글 데이터를 담은 DB 테이블이 있어야 합니다</sub>

## 기능
* 글, 댓글 열람
* 글 제목, 글 내용, 닉네임, ID&IP 검색 기능
* 개념글, 날짜별 필터링 & 검색
* 삭제 요청 기능 (실시간으로 디시 글 상태 체크 후 글삭됐을 시 삭제처리, python 런타임 필요)
* 통계 기능 (준비중)

## 스크린샷
![image](https://user-images.githubusercontent.com/88251502/141685123-2678f446-4c35-49bc-bc0a-6159cec777da.png)
![image](https://user-images.githubusercontent.com/88251502/141685229-9a977ec0-893b-45e6-8bcb-51f4c377ab3c.png)

## 알려진 문제
- 삭제된 댓글의 답글이 표시가 되지 않음
- 보이스리플 댓글 표시가 되지 않음
- webp 형식의 이미지가 표시되지 않음
- 새로운 형식의 비디오(제목, 조회수 등 포함된 형식)가 표시되지 않음
- User-Agent가 PC일 경우 본문 컨텐츠(이미지, 비디오, 투표 등)가 표시되지 않음 -> 디시 컨텐츠 서버에서 막은 것이기 때문에 해결 불가