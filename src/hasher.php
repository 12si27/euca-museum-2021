<?php

// 애유갤 전용 닉네임 hash 함수
// by 1227
// 2021.11.25 rev 1.0

function eucahash($input) {

    $input = strtolower($input);
    $hs = hash('crc32', $input);
    #$dec = base_convert($hs[4].$hs[5], 16, 10);
    $dec = $hs[4].$hs[5].$hs[6].$hs[7];
    
    $result = '';

    switch (base_convert($hs[0].$hs[1], 16, 10)%32):
        case 0: $result .= '씩씩한'; break;
        case 1: $result .= '착한'; break;
        case 2: $result .= '나쁜'; break;
        case 3: $result .= '괘씸한'; break;
        case 4: $result .= '용감한'; break;  
        case 5: $result .= '응큼한'; break;
        case 6: $result .= '못생긴'; break;
        case 7: $result .= '잘생긴'; break;
        case 8: $result .= '우쭐한'; break;
        case 9: $result .= '뻘쭘한'; break; 
        case 10: $result .= '기괴한'; break;
        case 11: $result .= '얌전한'; break;
        case 12: $result .= '깐죽대는'; break;
        case 13: $result .= '어린'; break;
        case 14: $result .= '늙은'; break;
        case 15: $result .= '평범한'; break;
        case 16: $result .= '성능좋은'; break;
        case 17: $result .= '겁없는'; break;
        case 18: $result .= '무모한'; break;
        case 19: $result .= '후달리는'; break;
        case 20: $result .= '갈망하는'; break;  
        case 21: $result .= '맛이 간'; break;
        case 22: $result .= '꼰대'; break;
        case 23: $result .= '응애'; break;
        case 24: $result .= '말년병장'; break;
        case 25: $result .= '알바생'; break; 
        case 26: $result .= '대통령'; break;
        case 27: $result .= '현직 교사'; break;
        case 28: $result .= '자영업자'; break;
        case 29: $result .= '신용불량자'; break;
        case 30: $result .= '벼락부자'; break;
        case 31: $result .= '코인쟁이'; break;
    endswitch;

    $result .= ' ';

    switch (base_convert($hs[2].$hs[3], 16, 10)%64):
        case 0: $result .= '권터'; break;
        case 1: $result .= '그웬'; break;
        case 2: $result .= '딜런'; break;
        case 3: $result .= '뚱이'; break;
        case 4: $result .= '라이언'; break;
        case 5: $result .= '레이븐'; break;
        case 6: $result .= '로빈'; break;
        case 7: $result .= '루시'; break;
        case 8: $result .= '루카'; break;
        case 9: $result .= '릭비'; break;
        case 10: $result .= '마르시'; break;
        case 11: $result .= '마르코'; break;
        case 12: $result .= '마셀린'; break;
        case 13: $result .= '마크'; break;
        case 14: $result .= '맥스'; break;
        case 15: $result .= '머슬맨'; break;
        case 16: $result .= '모디카이'; break;
        case 17: $result .= '배트맨'; break;
        case 18: $result .= '버블검'; break;
        case 19: $result .= '벤슨'; break;
        case 20: $result .= '보잭'; break;
        case 21: $result .= '브리짓'; break;
        case 22: $result .= '비모'; break;
        case 23: $result .= '사샤'; break;
        case 24: $result .= '쉬라'; break;
        case 25: $result .= '스킵스'; break;
        case 26: $result .= '스타'; break;
        case 27: $result .= '스타파이어'; break;
        case 28: $result .= '스테파니'; break;
        case 29: $result .= '스폰지밥'; break;
        case 30: $result .= '스프리그'; break;
        case 31: $result .= '호머'; break;
        case 32: $result .= '아이스퀸'; break;
        case 33: $result .= '아이스킹'; break;
        case 34: $result .= '알렉스'; break;
        case 35: $result .= '애미티'; break;
        case 36: $result .= '앤'; break;
        case 37: $result .= '에미'; break;
        case 38: $result .= '에밀리'; break;
        case 39: $result .= '오스카'; break;
        case 40: $result .= '웨비'; break;
        case 41: $result .= '유니키티'; break;
        case 42: $result .= '제니'; break;
        case 43: $result .= '제이크'; break;
        case 44: $result .= '조쉬'; break;
        case 45: $result .= '조안나'; break;
        case 46: $result .= '조커'; break;
        case 47: $result .= '주디'; break;
        case 48: $result .= '진저'; break;
        case 49: $result .= '캔디스'; break;
        case 50: $result .= '코라'; break;
        case 51: $result .= '클랜시'; break;
        case 52: $result .= '클레어'; break;
        case 53: $result .= '키포'; break;
        case 54: $result .= '튤립'; break;
        case 55: $result .= '트위그'; break;
        case 56: $result .= '페니'; break;
        case 57: $result .= '폴리'; break;
        case 58: $result .= '피오나'; break;
        case 59: $result .= '핀'; break;
        case 60: $result .= '헌위'; break;
        case 61: $result .= '헤지혹'; break;
        case 62: $result .= '힐다'; break;
        case 63: $result .= '로봇보이'; break;
    endswitch;

    return $result." #".$dec;
}


?>