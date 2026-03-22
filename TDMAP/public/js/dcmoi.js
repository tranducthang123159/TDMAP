/* =========================
LOAD ĐỊA CHÍNH MỚI (MapLibre)
========================= */

function loadDcMoi(data) {
    clearMeasure();

    if (map.getLayer("dc_moi_fill")) {
        map.off("click", "dc_moi_fill");
        map.off("dblclick", "dc_moi_fill");
    }

    if (map.getSource("dc_moi")) {
        if (map.getLayer("dc_moi_fill")) map.removeLayer("dc_moi_fill");
        if (map.getLayer("dc_moi_line")) map.removeLayer("dc_moi_line");
        map.removeSource("dc_moi");
    }

    map.addSource("dc_moi", {
        type: "geojson",
        data: data
    });

    map.addLayer({
        id: "dc_moi_fill",
        type: "fill",
        source: "dc_moi",
        paint: {
            "fill-color": "#ffd700",
    "fill-opacity": 0.35
        }
    });

    map.addLayer({
        id: "dc_moi_line",
        type: "line",
        source: "dc_moi",
        paint: {
            "line-color": "#ffd700",
    "line-width": 2
        }
    });

    map.on("click", "dc_moi_fill", function (e) {
        if (!e.features || e.features.length === 0) {
            alert("Không có thửa!");
            return;
        }

        let feature = e.features[0];

        window.currentFeature = feature;
        console.log("CLICK xong currentFeature =", window.currentFeature);

        highlightParcel(feature);
        showParcelInfo(feature);
        drawParcelMeasure(feature);
    });

    map.on("dblclick", "dc_moi_fill", function (e) {
        let lng = e.lngLat.lng;
        let lat = e.lngLat.lat;
        addMarker(lat, lng);
    });

    let bbox = turf.bbox(data);

    map.fitBounds(
        [
            [bbox[0], bbox[1]],
            [bbox[2], bbox[3]]
        ],
        {
            padding: 20
        }
    );

    initParcelSearch(data);
}