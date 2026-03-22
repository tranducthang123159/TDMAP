/* =========================
LOAD QUY HOẠCH (MapLibre)
========================= */

function loadQuyHoach(data) {
    if (!data || !data.features || !data.features.length) {
        console.warn("Không có dữ liệu quy hoạch");
        return;
    }

    data = turf.simplify(data, {
        tolerance: 0.00003,
        highQuality: false
    });

    /* tránh click nhân đôi */
    if (map.getLayer("quyhoach_fill")) {
        map.off("click", "quyhoach_fill");
    }

    /* xóa layer cũ */
    if (map.getLayer("quyhoach_fill")) {
        map.removeLayer("quyhoach_fill");
    }

    if (map.getLayer("quyhoach_line")) {
        map.removeLayer("quyhoach_line");
    }

    if (map.getSource("quy_hoach")) {
        map.removeSource("quy_hoach");
    }

    /* add source */
    map.addSource("quy_hoach", {
        type: "geojson",
        data: data
    });

    /* polygon fill */
    map.addLayer({
        id: "quyhoach_fill",
        type: "fill",
        source: "quy_hoach",
        paint: {
            "fill-color": [
                "coalesce",
                ["get", "fill"],
                "#ff0000"
            ],
            "fill-opacity": [
                "coalesce",
                ["to-number", ["get", "fill-opacity"]],
                0.25
            ]
        }
    });

    /* border */
    map.addLayer({
        id: "quyhoach_line",
        type: "line",
        source: "quy_hoach",
        paint: {
            "line-color": "#333333",
            "line-width": 0.5
        }
    });

    /* CLICK
       Nếu muốn giống file HTML hơn thì TẮT click đoạn này đi
       Nếu vẫn muốn bấm vào quy hoạch để xem info thì giữ lại
    */
    // map.on("click", "quyhoach_fill", function (e) {
    //     if (!e.features || e.features.length === 0) return;

    //     let feature = e.features[0];
    //     let p = feature.properties;

    //     highlightParcel(feature);
    //     drawParcelMeasure(feature);
    //     showInfo(p);
    // });

    /* zoom tới layer */
    let bbox = turf.bbox(data);

    map.fitBounds([
        [bbox[0], bbox[1]],
        [bbox[2], bbox[3]]
    ], {
        padding: 20
    });

    if (typeof initParcelSearch === "function") {
        initParcelSearch(data);
    }
}