/* =========================
LOAD QUY HOẠCH (MapLibre)
========================= */

function loadQuyHoach(data) {
    clearMeasure();

    if (!data || !data.features || !data.features.length) {
        console.warn("Không có dữ liệu quy hoạch");
        return;
    }

    let safeData = {
        type: "FeatureCollection",
        features: data.features.filter(f => {
            return (
                f &&
                f.type === "Feature" &&
                f.geometry &&
                f.geometry.type &&
                Array.isArray(f.geometry.coordinates)
            );
        })
    };

    if (!safeData.features.length) {
        console.warn("Không có feature hợp lệ");
        return;
    }

    const created = upsertGeoJSONSource("quy_hoach", safeData);

    if (created) {
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

        map.addLayer({
            id: "quyhoach_line",
            type: "line",
            source: "quy_hoach",
            paint: {
                "line-color": "#333333",
                "line-width": [
                    "interpolate", ["linear"], ["zoom"],
                    10, 0.3,
                    14, 0.6,
                    18, 1.2
                ]
            }
        });

        map.on("click", "quyhoach_fill", function (e) {
            const feature = e.features?.[0];
            if (!feature) return;

            window.currentFeature = feature;

            highlightParcel(feature);
            showParcelInfo(feature);
        });
    }

    if (
        window.currentMapMeta &&
        Array.isArray(window.currentMapMeta.bbox) &&
        window.currentMapMeta.bbox.length === 4
    ) {
        try {
            const bbox = window.currentMapMeta.bbox;

            map.fitBounds(
                [
                    [bbox[0], bbox[1]],
                    [bbox[2], bbox[3]]
                ],
                {
                    padding: 20,
                    duration: 0
                }
            );
        } catch (e) {
            console.warn("Không thể fitBounds cho quy hoạch:", e);
        }
    }

    // Quy hoạch thường không cần search thửa.
    // Nếu bạn thật sự cần thì mở lại đoạn dưới.
    /*
    if (typeof initParcelSearch === "function") {
        try {
            initParcelSearch(safeData);
        } catch (e) {
            console.warn("initParcelSearch quy hoạch lỗi:", e);
        }
    }
    */
}