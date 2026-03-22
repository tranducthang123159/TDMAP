/* =========================
LOAD ĐỊA CHÍNH CŨ
========================= */

function loadDcCu(data){

    clearMeasure();

    /* 🔥 SET KTT */
    updateVN2000(108.5);

    data = turf.simplify(data,{
        tolerance:0.00003,
        highQuality:false
    });

    /* remove event an toàn */
    if(map.getLayer("dc_cu_fill")){
        map.off("click","dc_cu_fill");
        map.off("dblclick","dc_cu_fill");
    }

    /* remove layer an toàn */
    if(map.getLayer("dc_cu_fill")) map.removeLayer("dc_cu_fill");
    if(map.getLayer("dc_cu_line")) map.removeLayer("dc_cu_line");
    if(map.getSource("dc_cu")) map.removeSource("dc_cu");

    /* add source */
    map.addSource("dc_cu",{
        type:"geojson",
        data:data
    });

    /* polygon */
    map.addLayer({
        id:"dc_cu_fill",
        type:"fill",
        source:"dc_cu",
        paint:{
            "fill-color":"#ff9900",
            "fill-opacity":0.15
        }
    });

    /* border */
    map.addLayer({
        id:"dc_cu_line",
        type:"line",
        source:"dc_cu",
        paint:{
            "line-color":"#ff9900",
            "line-width":1
        }
    });

    /* =========================
    CLICK THỬA
    ========================= */

    map.on("click","dc_cu_fill",function(e){

        if (!e.features || e.features.length === 0) {
            alert("Không có thửa!");
            return;
        }

        let feature = e.features[0];

        window.currentFeature = feature;

        highlightParcel(feature);
        showParcelInfo(feature);
        drawParcelMeasure(feature);

    });

    /* =========================
    DOUBLE CLICK
    ========================= */

    map.on("dblclick","dc_cu_fill",function(e){

        let lng = e.lngLat.lng;
        let lat = e.lngLat.lat;

        addMarker(lat,lng);

    });

    /* =========================
    ZOOM
    ========================= */

    let bbox = turf.bbox(data);

    map.fitBounds([
        [bbox[0],bbox[1]],
        [bbox[2],bbox[3]]
    ],{
        padding:20
    });

    /* search nếu có */
    if(typeof initParcelSearch === "function"){
        initParcelSearch(data);
    }

}