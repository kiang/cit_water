<?php
$rawPath = dirname(__DIR__) . '/raw';
$fc = array(
    'type' => 'FeatureCollection',
    'features' => array(),
);
$context = stream_context_create([
	'ssl' => [
		'verify_peer' => false,
		'verify_peer_name' => false,
	],
]);

$pageCount = 1;
$pageFile = $rawPath . '/page_' . $pageCount . '.json';
$url = 'https://sta.ci.taiwan.gov.tw/STA_WaterResource_v2/v1.0/Datastreams?$expand=Thing,Thing/Locations,Observations($orderby=phenomenonTime%20desc;$top=1)%20&$filter=substringof(%27Datastream_Category_type=%E6%B7%B9%E6%B0%B4%E6%84%9F%E6%B8%AC%E5%99%A8%27,Datastreams/description)%20and%20substringof(%27Datastream_Category=%E6%B7%B9%E6%B0%B4%E6%B7%B1%E5%BA%A6%27,Datastreams/description)';
$json = json_decode(file_get_contents($url, false, $context), true);
foreach($json['value'] AS $thing) {
    if(empty($thing['Observations'][0])) {
        continue;
    }
    $f = array(
        'type' => 'Feature',
        'properties' => $thing['Thing']['properties'],
    );
    foreach($thing['Thing']['Locations'] AS $location) {
        switch($location['encodingType']) {
            case 'application/vnd.geo+json':
                $f['geometry'] = $location['location'];
            break;
            case 'address':
                $f['properties']['address'] = $location['location']['address'];
            break;
        }
    }
    $f['properties']['unitOfMeasurement'] = $thing['unitOfMeasurement']['symbol'];
    $f['properties']['result'] = $thing['Observations'][0]['result'];
    $f['properties']['phenomenonTime'] = $thing['Observations'][0]['phenomenonTime'];
    $fc['features'][] = $f;
}
file_put_contents($pageFile, json_encode($json,  JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
while(!empty($json['@iot.nextLink'])) {
    $json = json_decode(file_get_contents($json['@iot.nextLink'], false, $context), true);
    foreach($json['value'] AS $thing) {
        if(empty($thing['Observations'][0])) {
            continue;
        }
        $f = array(
            'type' => 'Feature',
            'properties' => $thing['Thing']['properties'],
        );
        foreach($thing['Thing']['Locations'] AS $location) {
            switch($location['encodingType']) {
                case 'application/vnd.geo+json':
                    $f['geometry'] = $location['location'];
                break;
                case 'address':
                    $f['properties']['address'] = $location['location']['address'];
                break;
            }
        }
        $f['properties']['unitOfMeasurement'] = $thing['unitOfMeasurement']['symbol'];
        $f['properties']['result'] = $thing['Observations'][0]['result'];
        $f['properties']['phenomenonTime'] = $thing['Observations'][0]['phenomenonTime'];
        $fc['features'][] = $f;
    }
    // $pageFile = $rawPath . '/page_' . (++$pageCount) . '.json';
    // file_put_contents($pageFile, json_encode($json,  JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}
if(!empty($fc['features'])) {
    file_put_contents(dirname(__DIR__) . '/docs/iot_water.json', json_encode($fc));
}
