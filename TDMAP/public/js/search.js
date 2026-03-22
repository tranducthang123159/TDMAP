/* =========================
SEARCH VN2000
========================= */

function searchVN() {
    let input = document.getElementById("vn_input").value.trim();

    /* tách theo dấu phẩy hoặc khoảng trắng */
    let parts = input.split(/[,\s]+/).filter(Boolean);

    if (parts.length !== 2) {
        alert("Nhập đúng dạng: X, Y");
        return;
    }

    let x = parseFloat(parts[0]);
    let y = parseFloat(parts[1]);

    if (isNaN(x) || isNaN(y)) {
        alert("Tọa độ VN2000 không hợp lệ");
        return;
    }

    /* VN2000 -> WGS84 */
    proj4.defs(
        "VN2000",
        "+proj=tmerc +lat_0=0 +lon_0=108.5 +k=0.9999 +x_0=500000 +y_0=0 +ellps=WGS84 +units=m +no_defs"
    );

    let result = proj4("VN2000", "EPSG:4326", [y, x]);

    let lng = result[0];
    let lat = result[1];

    /* bay tới */
    map.flyTo({
        center: [lng, lat],
        zoom: 18
    });

    /* marker */
    addMarker(lat, lng);
}


/* =========================
SEARCH WGS84
========================= */

function searchWGS() {
    let input = document.getElementById("wgs_input").value.trim();

    /* tách theo dấu phẩy hoặc khoảng trắng */
    let parts = input.split(/[,\s]+/).filter(Boolean);

    if (parts.length !== 2) {
        alert("Nhập đúng dạng: Lat, Lng");
        return;
    }

    let lat = parseFloat(parts[0]);
    let lng = parseFloat(parts[1]);

    if (isNaN(lat) || isNaN(lng)) {
        alert("Tọa độ WGS84 không hợp lệ");
        return;
    }

    map.flyTo({
        center: [lng, lat],
        zoom: 18
    });

    addMarker(lat, lng);
}


/* =========================
TOGGLE SEARCH PANEL
========================= */

function toggleSearchPanel() {
    let body = document.getElementById("searchBody");
    let arrow = document.getElementById("searchArrow");

    body.classList.toggle("collapsed");

    if (body.classList.contains("collapsed")) {
        arrow.innerHTML = "▼";
    } else {
        arrow.innerHTML = "▲";
    }
}