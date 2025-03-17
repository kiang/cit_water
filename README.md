# Taiwan Flooding Sensor Data Visualization

This project collects and visualizes flooding sensor data from the [Civil IoT Taiwan platform](https://ci.taiwan.gov.tw/). The data is automatically fetched, processed, and displayed on an interactive map available at [https://kiang.github.io/flooding/](https://kiang.github.io/flooding/).

## Features

- Automated data collection from Civil IoT Taiwan's water resource API
- Data processing to extract relevant flooding information
- GeoJSON generation for map visualization
- Daily updates via GitHub Actions

## Data Source

The data comes from the Civil IoT Taiwan platform's water resource API, specifically focusing on flooding sensors (淹水感測器) that measure water depth (淹水深度).

## Project Structure

- `docs/` - Contains the processed GeoJSON data and web visualization
  - `iot_water.json` - The processed GeoJSON file used for visualization
- `raw/` - Stores raw data fetched from the API
- `scripts/` - Contains scripts for data collection and processing
  - `01_raw.php` - PHP script to fetch data from the API and generate GeoJSON
  - `cron.php` - Script for automated updates
  - `generate-geojson.bash` - Bash script alternative for generating GeoJSON

## How It Works

1. The scripts fetch flooding sensor data from the Civil IoT Taiwan platform
2. The data is processed and converted to GeoJSON format
3. The GeoJSON file is stored in the `docs/` directory
4. GitHub Pages serves the visualization at [https://kiang.github.io/flooding/](https://kiang.github.io/flooding/)

## Automation

The data is automatically updated through GitHub Actions or can be updated manually by running the scripts.

## License

This project is licensed under the terms included in the [LICENSE](LICENSE) file.

## Credits

- Data provided by [Civil IoT Taiwan](https://ci.taiwan.gov.tw/)
- Developed by [kiang](https://github.com/kiang) 
