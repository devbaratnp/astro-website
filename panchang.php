<?php
require_once __DIR__ . '/includes/public-header.php';
require_once __DIR__ . '/backend/lib/Panchang.php';

$date = $_GET['date'] ?? date('Y-m-d');

$panchang = null;
try {
    $panchang = Panchang::getForDate($date);
} catch (Throwable $e) {
    error_log('panchang calculation error: ' . $e->getMessage());
}

$bsMonthsList = ['बैशाख','जेठ','असार','श्रावण','भाद्र','आश्विन','कार्तिक','मंसिर','पौष','माघ','फाल्गुन','चैत्र'];
$bsDate = $panchang['bs_date'] ?? ['y' => 2070, 'm' => 1, 'd' => 1];
$bsDayNum = $bsDate['d'];
$bsMonthName = $bsMonthsList[$bsDate['m'] - 1] ?? '';
$bsYearNum = (string)$bsDate['y'];
$nepaliDigits = ['०','१','२','३','४','५','६','७','८','९'];
$bsDayNep = str_replace(range(0, 9), $nepaliDigits, (string)$bsDayNum);
$bsYearNep = str_replace(range(0, 9), $nepaliDigits, $bsYearNum);

$items = [];
$rashiNames = ['मेष', 'वृष', 'मिथुन', 'कर्कट', 'सिंह', 'कन्या', 'तुला', 'वृश्चिक', 'धनु', 'मकर', 'कुम्भ', 'मीन'];

try {
    $moonRashi = $panchang['moon_rashi'] ?? null;
    $moonRashiIndex = $moonRashi ? array_search($moonRashi, $rashiNames) : 0;
    if ($moonRashiIndex === false) $moonRashiIndex = 0;

    $rashiData = [
        1 => ['zodiac_ne' => 'मेष', 'ruler_planet' => 'मंगल', 'moon_positions_interpretation' => [1 => 'पहिलो चन्द्रमा: शुभ फल दिने र मानसिक प्रसन्नता', 2 => 'दोस्रो चन्द्रमा: लाभ दिने', 3 => 'तेस्रो चन्द्रमा: सुख दिने', 4 => 'चौथो चन्द्रमा: झगडा र विवाद', 5 => 'पाँचौँ चन्द्रमा: विद्या र बुद्धि', 6 => 'छैठौँ चन्द्रमा: रोग तथा शत्रु भय', 7 => 'सातौँ चन्द्रमा: सम्मान र प्रतिष्ठा', 8 => 'आठौँ चन्द्रमा: दुःख र कष्ट', 9 => 'नवौँ चन्द्रमा: भाग्य उदय', 10 => 'दशौँ चन्द्रमा: कर्मसिद्धि र लाभ', 11 => 'एघारौँ चन्द्रमा: सर्वाङ्गीण शुभ फल', 12 => 'बाह्रौँ चन्द्रमा: घाटा र नोक्सानी'], 'remedy_tips' => 'एउटा रातो रूमाल सधैँ खल्तीमा राख्नाले धेरै कामहरू बन्ने तथा शुभ फल दिन्छ। पहिरनमा एक वस्त्र रातो धारणा गर्नु अति आवश्यक छ।', 'infeasible_transit_moon' => 'शुक्रबार महत्वपूर्ण कामको थालनी गर्नु मनासिब हुँदैन।'],
        2 => ['zodiac_ne' => 'वृष', 'ruler_planet' => 'शुक्र', 'moon_positions_interpretation' => [1 => 'पहिलो चन्द्रमा: शुभ फल दिने र मानसिक प्रसन्नता', 2 => 'दोस्रो चन्द्रमा: लाभ दिने', 3 => 'तेस्रो चन्द्रमा: सुख दिने', 4 => 'चौथो चन्द्रमा: झगडा र विवाद', 5 => 'पाँचौँ चन्द्रमा: विद्या र बुद्धि', 6 => 'छैठौँ चन्द्रमा: रोग तथा शत्रु भय', 7 => 'सातौँ चन्द्रमा: सम्मान र प्रतिष्ठा', 8 => 'आठौँ चन्द्रमा: दुःख र कष्ट', 9 => 'नवौँ चन्द्रमा: भाग्य उदय', 10 => 'दशौँ चन्द्रमा: कर्मसिद्धि र लाभ', 11 => 'एघारौँ चन्द्रमा: सर्वाङ्गीण शुभ फल', 12 => 'बाह्रौँ चन्द्रमा: घाटा र नोक्सानी'], 'remedy_tips' => 'एउटा सेतो रूमाल आफ्नो खल्तीमा राखिराख्नु अति उत्तम र लाभदायी हुन्छ। पहिरनमा कुनै एक वस्त्र सेतो हुनु निकै राम्रो मानिन्छ।', 'infeasible_transit_moon' => 'वृश्चिक राशिको चन्द्रमा परेको दिन कुनै पनि महत्वपूर्ण कार्य प्रारम्भ गर्नु हुँदैन।'],
        3 => ['zodiac_ne' => 'मिथुन', 'ruler_planet' => 'बुध', 'moon_positions_interpretation' => [1 => 'पहिलो चन्द्रमा: शुभ फल दिने र मानसिक प्रसन्नता', 2 => 'दोस्रो चन्द्रमा: लाभ दिने', 3 => 'तेस्रो चन्द्रमा: सुख दिने', 4 => 'चौथो चन्द्रमा: झगडा र विवाद', 5 => 'पाँचौँ चन्द्रमा: विद्या र बुद्धि', 6 => 'छैठौँ चन्द्रमा: रोग तथा शत्रु भय', 7 => 'सातौँ चन्द्रमा: सम्मान र प्रतिष्ठा', 8 => 'आठौँ चन्द्रमा: दुःख र कष्ट', 9 => 'नवौँ चन्द्रमा: भाग्य उदय', 10 => 'दशौँ चन्द्रमा: कर्मसिद्धि र लाभ', 11 => 'एघारौँ चन्द्रमा: सर्वाङ्गीण शुभ फल', 12 => 'बाह्रौँ चन्द्रमा: घाटा र नोक्सानी'], 'remedy_tips' => 'एउटा पहेँलो रूमाल आफ्नो खल्तीमा राखिराख्नु अति उत्तम र लाभदायी हुन्छ। पहिरनमा कुनै एक पहेँलो वा केशरी वस्त्र समावेश गर्नु निकै राम्रो मानिन्छ।', 'infeasible_transit_moon' => 'जुन दिन मिथुन राशिको चन्द्रमा हुन्छ, त्यस दिन महत्वपूर्ण कामको सुरुवात गर्नु हुँदैन।'],
        4 => ['zodiac_ne' => 'कर्कट', 'ruler_planet' => 'चन्द्रमा', 'moon_positions_interpretation' => [1 => 'पहिलो चन्द्रमा: शुभ फल दिने र मानसिक प्रसन्नता', 2 => 'दोस्रो चन्द्रमा: लाभ दिने', 3 => 'तेस्रो चन्द्रमा: सुख दिने', 4 => 'चौथो चन्द्रमा: झगडा र विवाद', 5 => 'पाँचौँ चन्द्रमा: विद्या र बुद्धि', 6 => 'छैठौँ चन्द्रमा: रोग तथा शत्रु भय', 7 => 'सातौँ चन्द्रमा: सम्मान र प्रतिष्ठा', 8 => 'आठौँ चन्द्रमा: दुःख र कष्ट', 9 => 'नवौँ चन्द्रमा: भाग्य उदय', 10 => 'दशौँ चन्द्रमा: कर्मसिद्धि र लाभ', 11 => 'एघारौँ चन्द्रमा: सर्वाङ्गीण शुभ फल', 12 => 'बाह्रौँ चन्द्रमा: घाटा र नोक्सानी'], 'remedy_tips' => 'सधैँ सेतो रूमाल गोजीमा राख्दा निकै शुभ र फाइदा हुन्छ। आफ्नो पहिरनमा एक वस्त्र सेतो धारणा गर्नु अति आवश्यक छ।', 'infeasible_transit_moon' => 'मकर राशिको चन्द्रमा परेका दिन कुनै पनि नयाँ काम थालनी नगरेकै वेश हुन्छ।'],
        5 => ['zodiac_ne' => 'सिंह', 'ruler_planet' => 'सूर्य', 'moon_positions_interpretation' => [1 => 'पहिलो चन्द्रमा: शुभ फल दिने र मानसिक प्रसन्नता', 2 => 'दोस्रो चन्द्रमा: लाभ दिने', 3 => 'तेस्रो चन्द्रमा: सुख दिने', 4 => 'चौथो चन्द्रमा: झगडा र विवाद', 5 => 'पाँचौँ चन्द्रमा: विद्या र बुद्धि', 6 => 'छैठौँ चन्द्रमा: रोग तथा शत्रु भय', 7 => 'सातौँ चन्द्रमा: सम्मान र प्रतिष्ठा', 8 => 'आठौँ चन्द्रमा: दुःख र कष्ट', 9 => 'नवौँ चन्द्रमा: भाग्य उदय', 10 => 'दशौँ चन्द्रमा: कर्मसिद्धि र लाभ', 11 => 'एघारौँ चन्द्रमा: सर्वाङ्गीण शुभ फल', 12 => 'बाह्रौँ चन्द्रमा: घाटा र नोक्सानी'], 'remedy_tips' => 'सूर्यका सबै रङ्गसँग मिल्ने वस्त्र धारण गर्न मनासिब हुन्छ। सधैँ रातो वा सुनौलो रङ्गको रूमाल गोजीमा राख्दा अपेक्षित लाभ प्राप्त गर्न सकिन्छ।', 'infeasible_transit_moon' => 'मीन राशिको चन्द्रमा हुँदा महत्वपूर्ण कामको प्रारम्भ गर्न शुभ मानिँदैन।'],
        6 => ['zodiac_ne' => 'कन्या', 'ruler_planet' => 'बुध', 'moon_positions_interpretation' => [1 => 'पहिलो चन्द्रमा: शुभ फल दिने र मानसिक प्रसन्नता', 2 => 'दोस्रो चन्द्रमा: लाभ दिने', 3 => 'तेस्रो चन्द्रमा: सुख दिने', 4 => 'चौथो चन्द्रमा: झगडा र विवाद', 5 => 'पाँचौँ चन्द्रमा: विद्या र बुद्धि', 6 => 'छैठौँ चन्द्रमा: रोग तथा शत्रु भय', 7 => 'सातौँ चन्द्रमा: सम्मान र प्रतिष्ठा', 8 => 'आठौँ चन्द्रमा: दुःख र कष्ट', 9 => 'नवौँ चन्द्रमा: भाग्य उदय', 10 => 'दशौँ चन्द्रमा: कर्मसिद्धि र लाभ', 11 => 'एघारौँ चन्द्रमा: सर्वाङ्गीण शुभ फल', 12 => 'बाह्रौँ चन्द्रमा: घाटा र नोक्सानी'], 'remedy_tips' => 'चट्ट हरियो रूमाल खल्तीमा राख्नाले मानसिक शान्ति मिल्नुका साथै शुभ फल प्राप्त हुन्छ। वस्त्र धारण गर्दा कुनै एक कपडा हरियो वा पहेँलो मिसाउनु शुभ मानिन्छ।', 'infeasible_transit_moon' => 'धनु राशिको चन्द्रमा परेको दिन महत्वपूर्ण कार्य गर्नु हुँदैन।'],
        7 => ['zodiac_ne' => 'तुला', 'ruler_planet' => 'शुक्र', 'moon_positions_interpretation' => [1 => 'पहिलो चन्द्रमा: शुभ फल दिने र मानसिक प्रसन्नता', 2 => 'दोस्रो चन्द्रमा: लाभ दिने', 3 => 'तेस्रो चन्द्रमा: सुख दिने', 4 => 'चौथो चन्द्रमा: झगडा र विवाद', 5 => 'पाँचौँ चन्द्रमा: विद्या र बुद्धि', 6 => 'छैठौँ चन्द्रमा: रोग तथा शत्रु भय', 7 => 'सातौँ चन्द्रमा: सम्मान र प्रतिष्ठा', 8 => 'आठौँ चन्द्रमा: दुःख र कष्ट', 9 => 'नवौँ चन्द्रमा: भाग्य उदय', 10 => 'दशौँ चन्द्रमा: कर्मसिद्धि र लाभ', 11 => 'एघारौँ चन्द्रमा: सर्वाङ्गीण शुभ फल', 12 => 'बाह्रौँ चन्द्रमा: घाटा र नोक्सानी'], 'remedy_tips' => 'सेतो रूमाल हरहमेसा साथमा राख्नाले सुख, शान्ति र सन्तोष मिल्दछ। पहिरनमा कुनै एक वस्त्र सेतो वा क्रिम कलर छनोट गर्नु पर्दछ।', 'infeasible_transit_moon' => 'कुम्भ राशिको चन्द्रमा परेको दिन शुभ कार्य गर्नु हुँदैन।'],
        8 => ['zodiac_ne' => 'वृश्चिक', 'ruler_planet' => 'मंगल', 'moon_positions_interpretation' => [1 => 'पहिलो चन्द्रमा: शुभ फल दिने र मानसिक प्रसन्नता', 2 => 'दोस्रो चन्द्रमा: लाभ दिने', 3 => 'तेस्रो चन्द्रमा: सुख दिने', 4 => 'चौथो चन्द्रमा: झगडा र विवाद', 5 => 'पाँचौँ चन्द्रमा: विद्या र बुद्धि', 6 => 'छैठौँ चन्द्रमा: रोग तथा शत्रु भय', 7 => 'सातौँ चन्द्रमा: सम्मान र प्रतिष्ठा', 8 => 'आठौँ चन्द्रमा: दुःख र कष्ट', 9 => 'नवौँ चन्द्रमा: भाग्य उदय', 10 => 'दशौँ चन्द्रमा: कर्मसिद्धि र लाभ', 11 => 'एघारौँ चन्द्रमा: सर्वाङ्गीण शुभ फल', 12 => 'बाह्रौँ चन्द्रमा: घाटा र नोक्सानी'], 'remedy_tips' => 'सधैँ रातो रङ्गको रूमाल चट्ट पट्याएर गोजीमा राख्दा अपेक्षित लाभ प्राप्त गर्न सकिन्छ र मानसिक शान्ति मिल्दछ। पहिरनमा रातो कपडाको सम्मिश्रण फलदायी हुन्छ।', 'infeasible_transit_moon' => 'मकर राशिको चन्द्रमा हुने दिन महत्वपूर्ण कामको थालनी गर्न वाञ्छनीय हुँदैन।'],
        9 => ['zodiac_ne' => 'धनु', 'ruler_planet' => 'बृहस्पति', 'moon_positions_interpretation' => [1 => 'पहिलो चन्द्रमा: शुभ फल दिने र मानसिक प्रसन्नता', 2 => 'दोस्रो चन्द्रमा: लाभ दिने', 3 => 'तेस्रो चन्द्रमा: सुख दिने', 4 => 'चौथो चन्द्रमा: झगडा र विवाद', 5 => 'पाँचौँ चन्द्रमा: विद्या र बुद्धि', 6 => 'छैठौँ चन्द्रमा: रोग तथा शत्रु भय', 7 => 'सातौँ चन्द्रमा: सम्मान र प्रतिष्ठा', 8 => 'आठौँ चन्द्रमा: दुःख र कष्ट', 9 => 'नवौँ चन्द्रमा: भाग्य उदय', 10 => 'दशौँ चन्द्रमा: कर्मसिद्धि र लाभ', 11 => 'एघारौँ चन्द्रमा: सर्वाङ्गीण शुभ फल', 12 => 'बाह्रौँ चन्द्रमा: घाटा र नोक्सानी'], 'remedy_tips' => 'पहेँलो रङ्गको रूमाल साथमा राख्नाले सधैँ काममा सफलता मिल्छ अनि सिद्धि प्राप्त हुन्छ। आफ्नो पहिरनमा पहेँलो रङ्गको वस्त्र मिसाउनु वा धारणा गर्नुपर्छ।', 'infeasible_transit_moon' => 'जुन दिन कुम्भ राशिको चन्द्रमा हुन्छ, सो दिन कुनै पनि महत्वपूर्ण कार्यमा हात नहाल्नु उचित हुन्छ।'],
        10 => ['zodiac_ne' => 'मकर', 'ruler_planet' => 'शनि', 'moon_positions_interpretation' => [1 => 'पहिलो चन्द्रमा: शुभ फल दिने र मानसिक प्रसन्नता', 2 => 'दोस्रो चन्द्रमा: लाभ दिने', 3 => 'तेस्रो चन्द्रमा: सुख दिने', 4 => 'चौथो चन्द्रमा: झगडा र विवाद', 5 => 'पाँचौँ चन्द्रमा: विद्या र बुद्धि', 6 => 'छैठौँ चन्द्रमा: रोग तथा शत्रु भय', 7 => 'सातौँ चन्द्रमा: सम्मान र प्रतिष्ठा', 8 => 'आठौँ चन्द्रमा: दुःख र कष्ट', 9 => 'नवौँ चन्द्रमा: भाग्य उदय', 10 => 'दशौँ चन्द्रमा: कर्मसिद्धि र लाभ', 11 => 'एघारौँ चन्द्रमा: सर्वाङ्गीण शुभ फल', 12 => 'बाह्रौँ चन्द्रमा: घाटा र नोक्सानी'], 'remedy_tips' => 'कालो वा नीलो रूमाल गोजीमा राखिरहने गर्नु पर्दछ, जसले मानसिक शान्ति अनि शुभ फल दिलाउँछ। कालो नीलो रङ्गको वस्त्र धारणा गर्दा धेरै कामहरू बन्दछन्।', 'infeasible_transit_moon' => 'तुला राशिको चन्द्रमा परेको दिन कुनै पनि महत्वपूर्ण कार्य प्रारम्भ गर्नु हुँदैन।'],
        11 => ['zodiac_ne' => 'कुम्भ', 'ruler_planet' => 'शनि', 'moon_positions_interpretation' => [1 => 'पहिलो चन्द्रमा: शुभ फल दिने र मानसिक प्रसन्नता', 2 => 'दोस्रो चन्द्रमा: लाभ दिने', 3 => 'तेस्रो चन्द्रमा: सुख दिने', 4 => 'चौथो चन्द्रमा: झगडा र विवाद', 5 => 'पाँचौँ चन्द्रमा: विद्या र बुद्धि', 6 => 'छैठौँ चन्द्रमा: रोग तथा शत्रु भय', 7 => 'सातौँ चन्द्रमा: सम्मान र प्रतिष्ठा', 8 => 'आठौँ चन्द्रमा: दुःख र कष्ट', 9 => 'नवौँ चन्द्रमा: भाग्य उदय', 10 => 'दशौँ चन्द्रमा: कर्मसिद्धि र लाभ', 11 => 'एघारौँ चन्द्रमा: सर्वाङ्गीण शुभ फल', 12 => 'बाह्रौँ चन्द्रमा: घाटा र नोक्सानी'], 'remedy_tips' => 'कालो वा नीलो रूमाल गोजीमा राखिरहने गर्नु पर्दछ, जसको प्रयोगले मानसिक शान्ति अनि शुभ फल प्राप्त हुन्छ। पहिरनमा कुनै एक वस्त्र कालो वा नीलो अवश्य धारण गर्नु जीवनोपयोगी हुन्छ।', 'infeasible_transit_moon' => 'जुन दिन सिंह राशिको चन्द्रमा हुन्छन्, सो दिन शुभ तथा महत्वपूर्ण कार्य गर्दा सफलता मिल्दैन।'],
        12 => ['zodiac_ne' => 'मीन', 'ruler_planet' => 'बृहस्पति', 'moon_positions_interpretation' => [1 => 'पहिलो चन्द्रमा: शुभ फल दिने र मानसिक प्रसन्नता', 2 => 'दोस्रो चन्द्रमा: लाभ दिने', 3 => 'तेस्रो चन्द्रमा: सुख दिने', 4 => 'चौथो चन्द्रमा: झगडा र विवाद', 5 => 'पाँचौँ चन्द्रमा: विद्या र बुद्धि', 6 => 'छैठौँ चन्द्रमा: रोग तथा शत्रु भय', 7 => 'सातौँ चन्द्रमा: सम्मान र प्रतिष्ठा', 8 => 'आठौँ चन्द्रमा: दुःख र कष्ट', 9 => 'नवौँ चन्द्रमा: भाग्य उदय', 10 => 'दशौँ चन्द्रमा: कर्मसिद्धि र लाभ', 11 => 'एघारौँ चन्द्रमा: सर्वाङ्गीण शुभ फल', 12 => 'बाह्रौँ चन्द्रमा: घाटा र नोक्सानी'], 'remedy_tips' => 'पहेँलो रङ्गको रूमाल गोजीमा राखेर हिँड्नु निकै लाभदायक हुन्छ। वस्त्रको छनोट गर्दा पनि पहेँलो वा रातो रङ्गको गर्नु फलदायी हुन्छ, यसले श्रीवृद्धिमा सहयोगी भूमिका निर्वाह गर्दछ। मंगलबारको दिन लेनदेनको व्यवहार नगरेकै उत्तम हुन्छ।', 'infeasible_transit_moon' => 'कर्कट राशिको चन्द्रमा परेको दिन कुनै पनि महत्वपूर्ण कार्य गर्नु हुँदैन।'],
    ];

    foreach ($rashiData as $id => $data) {
        $zodiacIndex = $id - 1;
        $moonPosition = ($moonRashiIndex - $zodiacIndex + 12) % 12 + 1;
        $interpretation = $data['moon_positions_interpretation'][(string)$moonPosition] ?? '';
        $items[] = [
            'zodiac_id' => $id,
            'zodiac_ne' => $data['zodiac_ne'],
            'ruler_planet' => $data['ruler_planet'],
            'moon_position' => $moonPosition,
            'moon_interpretation' => $interpretation,
            'remedy_tips' => $data['remedy_tips'],
            'infeasible_transit_moon' => $data['infeasible_transit_moon'],
        ];
    }
} catch (Throwable $e) {
    error_log('horoscope compute error: ' . $e->getMessage());
    $items = [];
}

$fmt = new IntlDateFormatter('ne-NP', IntlDateFormatter::LONG, IntlDateFormatter::NONE, 'Asia/Kathmandu');
$dateStr = $fmt->format(strtotime($date));

$tabs = ['पञ्चाङ्ग', 'दिन फल', 'रात्री फल'];

$panchangRows = [];
if ($panchang) {
    $labels = ['तिथि' => 'tithi', 'सूर्योदय' => 'sunrise', 'सूर्यास्त' => 'sunset', 'नक्षत्र' => 'nakshatra', 'करण' => 'karana', 'योग' => 'yoga'];
    foreach ($labels as $label => $key) {
        if (!empty($panchang[$key])) {
            $panchangRows[] = [$label, $panchang[$key]];
        }
    }
}

renderPublicHeader('आजको पञ्चाङ्ग र राशिफल | Astro Shree Hari', 'आजको तिथि, नक्षत्र, सूर्योदय, सूर्यास्त र दैनिक राशिफल हेर्नुहोस्। शास्त्रसम्मत पञ्चाङ्ग विवरण।', '/panchang', ['/assets/css/pages/panchang.css']);
?>
<section class="section page-section panchang-page">
  <div class="container panchang-shell">
    <div class="panchang-date-nav">
      <button type="button" class="nav-btn" id="prev-day" aria-label="अघिल्लो दिन">&lsaquo;</button>
      <div class="panchang-date-bs">
        <small class="panchang-date-label">मिति (नेपाली)</small>
        <div class="bs-date-row">
          <div class="bs-date-field">
            <select id="bs-year"></select>
          </div>
          <div class="bs-date-field">
            <select id="bs-month"></select>
          </div>
          <div class="bs-date-field">
            <select id="bs-day"></select>
          </div>
        </div>
      </div>
      <button type="button" class="nav-btn" id="next-day" aria-label="अर्को दिन">&rsaquo;</button>
    </div>

    <header class="panchang-hero">
      <aside class="panchang-calendar">
        <strong id="hero-bs-day"><?php echo $bsDayNep; ?></strong>
        <b id="hero-bs-date"><?php echo htmlspecialchars($bsMonthName, ENT_QUOTES, 'UTF-8'); ?> <?php echo htmlspecialchars($bsYearNep, ENT_QUOTES, 'UTF-8'); ?></b>
        <span>आजको पञ्चाङ्ग</span>
        <i class="panchang-moon" aria-hidden="true"></i>
      </aside>
      <div>
        <span class="panchang-kicker">दैनिक अपडेट</span>
        <h1>आजको पञ्चाङ्ग</h1>
        <div class="panchang-summary">
          <div>तिथि<strong id="summary-tithi"><?php echo htmlspecialchars($panchang['tithi'] ?? 'लोड हुँदैछ…', ENT_QUOTES, 'UTF-8'); ?></strong></div>
          <div>नक्षत्र<strong id="summary-nakshatra"><?php echo htmlspecialchars($panchang['nakshatra'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></strong></div>
          <div>सूर्योदय<strong id="summary-sunrise"><?php echo htmlspecialchars($panchang['sunrise'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></strong></div>
          <div>सूर्यास्त<strong id="summary-sunset"><?php echo htmlspecialchars($panchang['sunset'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></strong></div>
        </div>
      </div>
    </header>

    <?php $events = $panchang['special_events'] ?? []; if (count($events) > 0): ?>
    <div class="panchang-events">
      <?php foreach ($events as $ev): ?>
        <span class="panchang-event-tag"><?php echo htmlspecialchars($ev['ne'] ?? $ev['en'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="panchang-tabs" role="tablist">
      <?php foreach ($tabs as $tab): ?>
        <button type="button" role="tab" class="tab-btn <?php echo $tab === 'पञ्चाङ्ग' ? 'active' : ''; ?>" data-tab="<?php echo htmlspecialchars($tab, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($tab, ENT_QUOTES, 'UTF-8'); ?></button>
      <?php endforeach; ?>
    </div>

    <section class="panchang-detail-card" id="panchang-detail">
      <h2 id="detail-title">पञ्चाङ्ग</h2>
      <hr />

      <div class="tab-content" id="tab-panchang" style="display:block">
        <?php if (count($panchangRows) > 0): ?>
          <div class="panchang-facts">
            <?php foreach ($panchangRows as $row): ?>
              <div><b><?php echo htmlspecialchars($row[0], ENT_QUOTES, 'UTF-8'); ?></b><span><?php echo htmlspecialchars($row[1], ENT_QUOTES, 'UTF-8'); ?></span></div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <p class="panchang-placeholder">पञ्चाङ्ग विवरण अहिले उपलब्ध छैन।</p>
        <?php endif; ?>
      </div>

      <div class="tab-content" id="tab-day" style="display:none">
        <div class="panchang-forecast-grid" id="day-forecast-grid">
          <?php
          $dayEntries = array_filter($items, function($it) { return !empty($it['moon_interpretation']); });
          foreach ($dayEntries as $it):
          ?>
            <article class="panchang-forecast-card">
              <h3><?php echo htmlspecialchars($it['zodiac_ne'], ENT_QUOTES, 'UTF-8'); ?></h3>
              <p><?php echo htmlspecialchars($it['moon_interpretation'], ENT_QUOTES, 'UTF-8'); ?></p>
            </article>
          <?php endforeach; ?>
          <?php if (count($dayEntries) === 0): ?>
            <p class="panchang-placeholder">विवरण उपलब्ध छैन।</p>
          <?php endif; ?>
        </div>
      </div>

      <div class="tab-content" id="tab-night" style="display:none">
        <div class="panchang-forecast-grid" id="night-forecast-grid">
          <?php
          $nightEntries = array_filter($items, function($it) { return !empty($it['remedy_tips']); });
          foreach ($nightEntries as $it):
          ?>
            <article class="panchang-forecast-card">
              <h3><?php echo htmlspecialchars($it['zodiac_ne'], ENT_QUOTES, 'UTF-8'); ?></h3>
              <p><?php echo htmlspecialchars($it['remedy_tips'], ENT_QUOTES, 'UTF-8'); ?></p>
              <?php if (!empty($it['infeasible_transit_moon'])): ?>
                <small><?php echo htmlspecialchars($it['infeasible_transit_moon'], ENT_QUOTES, 'UTF-8'); ?></small>
              <?php endif; ?>
            </article>
          <?php endforeach; ?>
          <?php if (count($nightEntries) === 0): ?>
            <p class="panchang-placeholder">विवरण उपलब्ध छैन।</p>
          <?php endif; ?>
        </div>
      </div>
    </section>

    <p class="panchang-notice">विवरण प्रशासनिक र गणनात्मक डाटामा आधारित छ।</p>

    <h2 class="subheading">आजको राशिफल</h2>
    <div class="horoscope-grid" id="horoscope-grid">
      <?php foreach ($items as $it): ?>
        <article class="horoscope-card">
          <h3><?php echo htmlspecialchars($it['zodiac_ne'], ENT_QUOTES, 'UTF-8'); ?></h3>
          <p><?php echo htmlspecialchars($it['moon_interpretation'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<script id="panchang-data" type="application/json">
<?php echo json_encode([
    'panchang' => $panchang,
    'horoscope' => ['items' => $items],
    'bs_initial' => $bsDate,
], JSON_UNESCAPED_UNICODE); ?>
</script>
<script id="panchang-bs-months" type="application/json"><?php echo json_encode($bsMonthsList, JSON_UNESCAPED_UNICODE); ?></script>
<script id="panchang-bs-data" type="application/json"><?php echo json_encode(Panchang::getBsData()); ?></script>
<?php
renderPublicFooter(['/assets/js/panchang.js']);
