    <!DOCTYPE html>
    <html lang="ku" dir="rtl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>بەڕێوەبردنی ئۆتۆمبیلەکان</title>

        <style>
            * { box-sizing: border-box; }
            body {
                margin: 0;
                font-family: Tahoma, Arial, sans-serif;
                background: #f4f6f8;
                color: #202938;
            }
            .topbar {
                background: #123b5d;
                color: white;
                padding: 18px 5%;
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 16px;
            }
            .brand { font-size: 20px; font-weight: bold; }
            .subtitle { font-size: 13px; color: #d5e4ef; margin-top: 5px; }
            .badge {
                background: #e7f4ed;
                color: #24734b;
                padding: 8px 12px;
                border-radius: 20px;
                font-size: 12px;
                white-space: nowrap;
            }
            main { width: min(1100px, 92%); margin: 28px auto; }
            .notice {
                background: #fff8df;
                border: 1px solid #f1dfa1;
                color: #765c10;
                padding: 13px 16px;
                border-radius: 10px;
                margin-bottom: 22px;
                line-height: 1.8;
            }
            h1 { font-size: 25px; margin: 0 0 7px; }
            .intro { color: #687586; margin: 0 0 22px; }
            .cards {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 16px;
                margin-bottom: 22px;
            }
            .card, .panel {
                background: white;
                border: 1px solid #e6eaf0;
                border-radius: 13px;
                padding: 20px;
                box-shadow: 0 3px 12px #18344a0b;
            }
            .card-title { color: #687586; font-size: 14px; }
            .number { font-size: 30px; font-weight: bold; margin-top: 12px; }
            .green { color: #258256; }
            .blue { color: #2867a5; }
            .orange { color: #c47b21; }
            .panel h2 { font-size: 18px; margin: 0 0 16px; }
            .table-wrap { overflow-x: auto; }
            table { width: 100%; border-collapse: collapse; min-width: 600px; }
            th, td {
                text-align: right;
                border-bottom: 1px solid #edf0f3;
                padding: 13px 10px;
                font-size: 14px;
            }
            th { color: #687586; font-weight: normal; }
            .status {
                display: inline-block;
                padding: 6px 10px;
                border-radius: 20px;
                background: #fff0dc;
                color: #9b5c11;
                font-size: 12px;
            }
            .bottom { color: #7b8794; text-align: center; font-size: 12px; padding: 25px 0; }
            @media (max-width: 700px) {
                .cards { grid-template-columns: 1fr; }
                .topbar { align-items: flex-start; flex-direction: column; }
                h1 { font-size: 21px; }
            }
        </style>
    </head>

    <body>
        <header class="topbar">
            <div>
                <div class="brand">بەڕێوەبردنی ئۆتۆمبیلەکان</div>
                <div class="subtitle">بەڕێوەبەرایەتی گومرگی سلێمانی</div>
            </div>
            <div class="badge">نموونەی ڕووکار — داتاکان ساختەن</div>
        </header>

        <main>
            <div class="notice">
                ئەمە تەنها قۆناغی یەکەمی نموونەیە. ژمارە و ناوەکان ساختەن؛ هێشتا بە بنکەدراوە
                یان چوونەژوورەوەوە نەبەستراوەتەوە.
            </div>

            <h1>داشبۆرد</h1>
            <p class="intro">پوختەی دۆخی ئۆتۆمبیلەکان و جووڵەکانی ئەمڕۆ</p>

            <section class="cards">
                <div class="card">
                    <div class="card-title">کۆی ئۆتۆمبیلەکان</div>
                    <div class="number blue">12</div>
                </div>
                <div class="card">
                    <div class="card-title">ئۆتۆمبیلی بەردەست</div>
                    <div class="number green">9</div>
                </div>
                <div class="card">
                    <div class="card-title">ئۆتۆمبیل لە دەرەوە</div>
                    <div class="number orange">3</div>
                </div>
            </section>

            <section class="panel">
                <h2>جووڵەی نموونەیی ئۆتۆمبیلەکان</h2>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>ژمارەی ئۆتۆمبیل</th>
                                <th>شۆفێر</th>
                                <th>شوێنی مەبەست</th>
                                <th>کاتی دەرچوون</th>
                                <th>دۆخ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>سلێمانی 12345</td>
                                <td>شۆفێری نموونە ١</td>
                                <td>ناوچەی نموونە</td>
                                <td>08:30</td>
                                <td><span class="status">لە دەرەوەیە</span></td>
                            </tr>
                            <tr>
                                <td>سلێمانی 67890</td>
                                <td>شۆفێری نموونە ٢</td>
                                <td>شوێنی نموونە</td>
                                <td>09:15</td>
                                <td><span class="status">لە دەرەوەیە</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <div class="bottom">ئەپەکە هێشتا لە قۆناغی دروستکردندایە</div>
        </main>
    </body>
    </html>
