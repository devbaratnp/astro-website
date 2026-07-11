<?php
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../includes/helpers.php';
$signs = ['मेष','वृष','मिथुन','कर्कट','सिंह','कन्या','तुला','वृश्चिक','धनु','मकर','कुम्भ','मीन'];
$messages = ['आज धैर्य र स्पष्ट संवादले लाभ दिनेछ।','नयाँ काम सुरु गर्नुअघि योजना पुनः जाँच्नुहोस्।','परिवार र स्वास्थ्यका लागि समय छुट्याउनु शुभ हुनेछ।','आर्थिक निर्णयमा संयम अपनाउनुहोस्।'];
$date = $_GET['date'] ?? date('Y-m-d');
$items = [];
foreach ($signs as $i => $sign) $items[] = ['sign'=>$sign,'forecast'=>$messages[(crc32($date.$i)&0x7fffffff)%count($messages)]];
jsonSuccess(['date'=>$date,'items'=>$items,'disclaimer'=>'यो सामान्य दैनिक संकेत हो; व्यक्तिगत निर्णयका लागि विस्तृत परामर्श लिनुहोस्।']);
