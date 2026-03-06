let geoLayer;
let geoData;
let labelLayer;

function loadMap() {

    let file = document.getElementById("geojsonFile").files[0];

    if (!file) {
        alert("Chọn file GeoJSON");
        return;
    }

    let reader = new FileReader();

    reader.onload = function (e) {

        geoData = JSON.parse(e.target.result);

        if (geoLayer) {
            map.removeLayer(geoLayer);
        }

        geoLayer = L.geoJSON(geoData, {

            style: {
                color: "yellow",
                weight: 1,
                fillOpacity: 0
            },

            onEachFeature: function (feature, layer) {

                layer.on("click", function () {

                    highlight(layer);

                    showInfo(feature.properties);

                    drawEdges(layer);

                });

            }

        }).addTo(map);

        map.fitBounds(geoLayer.getBounds());

    };

    reader.readAsText(file);

}



function highlight(layer) {

    geoLayer.eachLayer(function (l) {
        geoLayer.resetStyle(l);
    });

    layer.setStyle({
        color: "red",
        weight: 3,
        fillOpacity: 0.2
    });

    map.fitBounds(layer.getBounds());

}



function drawEdges(layer) {

    labelLayer.clearLayers();

    let latlngs = layer.getLatLngs();

    if (!latlngs) return;

    let coords;

    // xử lý polygon hoặc multipolygon
    if (Array.isArray(latlngs[0][0])) {
        coords = latlngs[0][0];
    } else {
        coords = latlngs[0];
    }

    if (coords.length < 3) return;



    // ===== HIỆN ĐIỂM =====

    coords.forEach((p, i) => {

        L.marker(p, {
            icon: L.divIcon({
                className: "",
                html: `<div class="pointLabel">${i + 1}</div>`
            })
        }).addTo(labelLayer);

    });



    // ===== HIỆN CHIỀU DÀI CẠNH =====

    for (let i = 0; i < coords.length; i++) {

        let p1 = coords[i];
        let p2 = coords[(i + 1) % coords.length];

        let dist = p1.distanceTo(p2);

        let mid = L.latLng(
            (p1.lat + p2.lat) / 2,
            (p1.lng + p2.lng) / 2
        );

        L.marker(mid, {
            icon: L.divIcon({
                className: "",
                html: `<div class="edgeLabel">${dist.toFixed(2)} m</div>`
            })
        }).addTo(labelLayer);

    }



    // ===== TÍNH DIỆN TÍCH =====

    let area = polygonArea(coords);

    let center = layer.getBounds().getCenter();

    L.marker(center, {
        icon: L.divIcon({
            className: "",
            html: `<div class="areaLabel">${area.toFixed(2)} m²</div>`
        })
    }).addTo(labelLayer);

}



function polygonArea(coords) {

    let area = 0;

    for (let i = 0; i < coords.length; i++) {

        let j = (i + 1) % coords.length;

        area += coords[i].lng * coords[j].lat;
        area -= coords[j].lng * coords[i].lat;

    }

    area = Math.abs(area / 2);

    area = area * 111319 * 111319;

    return area;

}



function showInfo(p) {

    document.getElementById("info").innerHTML = `

<b>ID:</b> ${p.ID || ""} <br>
<b>Tờ:</b> ${p.SHBANDO || ""} <br>
<b>Thửa:</b> ${p.SHTHUA || ""} <br>
<b>Diện tích:</b> ${p.DIENTICH || ""} m² <br>
<b>Loại đất:</b> ${p.KHLOAIDAT || ""} <br>
<b>Tên chủ:</b> ${p.TENCHU || ""} <br>
<b>Địa chỉ:</b> ${p.DIACHI || ""}

`;

}



function searchParcel() {

    let to = document.getElementById("to").value;
    let thua = document.getElementById("thua").value;

    geoLayer.eachLayer(function (layer) {

        let p = layer.feature.properties;

        if (p.SHBANDO == to && p.SHTHUA == thua) {

            highlight(layer);

            showInfo(p);

            drawEdges(layer);

        }

    });

}



function clearLabels() {

    labelLayer.clearLayers();

}


