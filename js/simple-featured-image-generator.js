jQuery(document).ready(function($) {
    // Canvas とコンテキスト取得
    var canvas = document.getElementById('egf_generator_canvas');
    var ctx    = canvas.getContext('2d');

    // ヘッダー画像をバックグラウンドに描画
    var headerImage = new Image();
    headerImage.src = egfData.headerImage;
    headerImage.onload = function() {
        ctx.drawImage(headerImage, 0, 0, canvas.width, canvas.height);
    };

    // 「作成」ボタンクリックイベント
    $('#egf_generate').on('click', function() {
        // Canvas クリアと背景再描画
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        if (headerImage.complete) {
            ctx.drawImage(headerImage, 0, 0, canvas.width, canvas.height);
        }

        // 背景の塗りつぶし
        ctx.fillStyle = $('#egf_framecolor').val();
        ctx.fillRect(0,0,canvas.width,canvas.height);

        // タイトル範囲の塗りつぶし
        var frameWidth = 32;
        var frameRadius = 32;
        ctx.fillStyle = $('#egf_bgcolor').val();
        ctx.fillRect(frameWidth, frameWidth+frameRadius, canvas.width - (frameWidth * 2), canvas.height - ((frameWidth+frameRadius)*2));
        ctx.fillRect(frameWidth+frameRadius, frameWidth, canvas.width - ((frameWidth+frameRadius)*2), canvas.height - (frameWidth * 2));
        ctx.arc(frameWidth+frameRadius, frameWidth+frameRadius, frameRadius, 0, 2* Math.PI);
        ctx.fill();
        ctx.arc(canvas.width - (frameWidth + frameRadius), frameWidth+frameRadius, frameRadius, 0, 2* Math.PI);
        ctx.fill();
        ctx.arc(frameWidth+frameRadius, canvas.height - (frameWidth + frameRadius), frameRadius, 0, 2* Math.PI);
        ctx.fill();
        ctx.arc(canvas.width - (frameWidth + frameRadius), canvas.height - (frameWidth + frameRadius), frameRadius, 0, 2* Math.PI);
        ctx.fill();

        // タイトル取得
        var title1 = egfData.postTitle;
        var title2 = egfData.authorName;
        console.log(title1);
        console.log(title2);

        // テキスト描画設定
        ctx.textAlign = 'left';
        ctx.fillStyle = $('#egf_fontcolor').val();

        // タイトルの描画
        var fontSize = 64;
        var padding = fontSize * 2;
        var toppadding = fontSize * 1.5;
        var lineWidth = canvas.width - (padding * 2);
        ctx.font = fontSize + 'px meiryo bold';

        var column=[""], line = 0;
        for (var i = 0; i < title1.length; i++){
            var word = title1.charAt(i);
            if (ctx.measureText(column[line] + word).width > lineWidth) {
                line++;
                column[line] = '';
            }
            column[line] += word;
        }

        for(var i in column){
            ctx.fillText(column[i],
                    padding,
                    toppadding + ((parseInt(i) + 1) * fontSize) + (parseInt(i) * fontSize /4));
        }

        //// 投稿者の描画
        var fontSize2 = 48;
        var avatarSize = 96;
        var avatar = new Image();

        avatar.src = egfData.authorAvatar;
        ctx.drawImage(avatar, padding, canvas.height - toppadding - (fontSize2* 2), avatarSize, avatarSize);

        ctx.font = fontSize2 + 'px meiryo bold';
        ctx.fillText(title2, padding + avatarSize + (fontSize2/2), canvas.height - (toppadding + (fontSize2/2)));

        // 保存ボタンを有効化
        $('#egf_save').prop('disabled', false);
    });

    // 「保存してアイキャッチに設定」ボタンクリックイベント
    $('#egf_save').on('click', function() {
        var dataURL = canvas.toDataURL('image/png');
        $.post(
            egfData.ajaxUrl,
            {
                action: 'egf_save_image',
                nonce: egfData.nonce,
                postId: egfData.postId,
                imageData: dataURL
            }
        )
        .done(function(response) {
            if (response.success) {
                // 結果イメージを更新して表示
                $('#egf_result_img').attr('src', response.data.url).show();
            } else {
                alert('Error: ' + response.data.message);
            }
        })
        .fail(function() {
            alert('AJAX通信に失敗しました');
        });
    });

    // 初期イメージがある場合は保存ボタンを有効化
    if (egfData.initialImage) {
        $('#egf_save').prop('disabled', false);
    } else {
        $('#egf_save').prop('disabled', true);
    }
});
