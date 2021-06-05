#!/bin/bash
set -eo pipefail

url='https://sta.ci.taiwan.gov.tw/STA_WaterResource_v2/v1.0/Datastreams?$expand=Thing,Thing/Locations,Observations($orderby=phenomenonTime+desc;$top=1)+&$filter=substringof(%27Datastream_Category_type=%E6%B7%B9%E6%B0%B4%E6%84%9F%E6%B8%AC%E5%99%A8%27,Datastreams/description)'
key_next=@iot.nextLink
mkdir -p docs/ raw/ 
rm raw/* || true
count=1

paged_file() { echo raw/page_$count.json; }

while true; do
  if [ -e $(paged_file) ]; then 
    link=$(jq -r .\"$key_next\" $(paged_file))
    ((count++))
    [[ $link == null ]] && break
  else 
    link=$url
  fi

  curl --globoff $link \
  | tee $(paged_file) \
  | jq '.value[] |
    select(.Observations!=null) |
    .properties                   = .Thing.properties |
    .properties.unitOfMeasurement = .unitOfMeasurement.symbol |
    .properties.result            = .Observations[0].result |
    .properties.phenomenonTime    = .Observations[0].phenomenonTime |
    .geometry                     = ([.Thing.Locations[] | select(.encodingType=="application/vnd.geo+json")][0].location) |
    { type: "Feature", properties: .properties, geometry: .geometry }'
done \
| jq -s '{type: "FeatureCollection", features: .}' >docs/iot_water.json
