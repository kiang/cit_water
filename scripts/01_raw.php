<?php
$rawPath = dirname(__DIR__) . '/raw';

$url = 'https://sta.ci.taiwan.gov.tw/STA_WaterResource_v2/v1.0/Datastreams?$expand=Thing,Thing/Locations,Observations($orderby=phenomenonTime%20desc;$top=1)%20&$filter=substringof(%27Datastream_Category_type=%E6%B7%B9%E6%B0%B4%E6%84%9F%E6%B8%AC%E5%99%A8%27,Datastreams/description)';
$json = json_decode(file_get_contents($url), true);
$pageCount = 1;
$pageFile = $rawPath . '/page_' . $pageCount . '.json';
file_put_contents($pageFile, json_encode($json,  JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
while(!empty($json['@iot.nextLink'])) {
    $json = json_decode(file_get_contents($json['@iot.nextLink']), true);
    $pageFile = $rawPath . '/page_' . (++$pageCount) . '.json';
    file_put_contents($pageFile, json_encode($json,  JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}