<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <title>EncodePage</title>
        <!-- Bootstrap CSS CDN -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">

        <!-- Font Awesome JS -->
        <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/solid.js" integrity="sha384-tzzSw1/Vo+0N5UhStP3bvwWPq+uvzCMfrN1fEFe+xBmv1C/AtVX5K0uZtmcHitFZ" crossorigin="anonymous"></script>
        <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/fontawesome.js" integrity="sha384-6OIrr52G08NpOFSZdxxz1xdNSndlD4vdcf/q2myIUVO0VsqaGHJsB0RaBE01VTOY" crossorigin="anonymous"></script>

    </head>
    <body>
        <div class="wrapper">
            <div class="row">
                <!-- Sidebar -->
                <div class="col-2" id = 'side_bar'>
                    <div class="sidebar-header">
                        <h4>編碼題目列表</h4>
                    </div>

                    <ul class="list-unstyled components" id='bar_items'>
                    </ul>
                </div>
                <!-- Page Content -->
                <div class="col-10" id = 'content'>
                    <div class="form-group">
                        <label >原始文章</label>
                        <p class="text-left" id="origin">{原始文章顯示位置}</p>
                        <button type="button" id='extract_btn1' onclick = "pick_quote()" class="btn btn-primary">擷取引述</button><br>
                        <label >報導引述內容</label>
                        <p class="text-left" id="quote_content">{引述顯示位置}</p>
                        <button type="button" id='extract_btn2' onclick = "pick_source()" class="btn btn-primary">擷取消息來源</button><br>
                        <label >消息來源名稱</label>
                        <p class="text-left" id="quote_origin">{消息來源名稱}</p>
                        <label >消息來源本名</label>
                        <textarea class="form-control" rows="1" id="quote_actual" placeholder="{消息來源本名}"></textarea>
                        <label >引述對象位置</label>
                        <label class="radio-inline"><input type="radio" name="optradio" checked value=0>0（切分單位前）</label>
                        <label class="radio-inline"><input type="radio" name="optradio" value=1>1（切分單位中）</label>
                        <label class="radio-inline"><input type="radio" name="optradio" value=2>2（切分單位後）</label>
                        <label class="radio-inline"><input type="radio" name="optradio" value=3>無</label>
                    </div>
                    <button type="button" onclick = "send_result()" class="btn btn-primary">送出</button>
                </div>
            </div>
        </div>       

       <script> 
        var encoderUuid = {!!json_encode($encoderUuid) !!};
        var question_list = {!! json_encode($question_list) !!};
        var question_pos = -1
        var item_sum
        var current_item = null

        window.onload = function () {
            var items = document.getElementById('bar_items')
            var item_sum = question_list.length
            for( var i=0; i<item_sum; i++) {
                question = question_list[i]
                var tmplist = document.createElement("li");
                tmplist.innerText = question['article_id']
                tmplist.value = i
                tmplist.setAttribute('onclick','switch_object(this);'); // for FF
                tmplist.onclick = function() {switch_object(this);}; // for IE
                if(question['is_answered']) {
                    tmplist.classList.add('has_answered')
                } else {
                    tmplist.classList.add('not_answered')
                    if (question_pos < 0) {
                        question_pos = i
                    }
                }
                items.appendChild(tmplist)
            }
            //如果都回答過了，則回到第一題
            if (question_pos < 0) {
                question_pos = 0
            }
            //載入題目
            current_article_id = question_list[question_pos]['article_id']
            update_object(current_article_id)
        }
        
        function switch_object(obj) {
            question_pos = obj.value
            current_article_id = question_list[question_pos]['article_id']
            update_object(current_article_id)
        }

        function update_object(article_id) {
            axios.get('/api/encoders/'+encoderUuid+'/articles/'+article_id)
            .then(function (response) {
                console.log('load_object '+article_id + ' '+response.status)
                if(response.status == 200) {
                    current_item = response.data
                    document.getElementById('origin').innerText = current_item.article.body
                    document.getElementById('quote_content').innerText = ''
                    document.getElementById('quote_origin').innerText = ''
                    document.getElementById('quote_actual').value = ''
                    document.getElementsByName('optradio')['0'].checked = true
                    if(current_item.result != null) {
                        fill_the_result()
                    }
                } else {
                    alert('載入題目錯誤')
                }
            })
            .catch(function (error) {
                console.log(error);
            });
        }

        function fill_the_result(){
            var result = current_item.result
            document.getElementById('quote_content').innerText = result['quote_content']
            document.getElementById('quote_origin').innerText = result['quote_origin']
            document.getElementById('quote_actual').value = result['quote_actual']
            if (result['quote_pos'] == 3 || result['quote_pos'] == null) {
                document.getElementsByName('optradio')['3'].checked = true
            } else {
                document.getElementsByName('optradio')[result['quote_pos']].checked = true
            }
        }

        function send_result(){
            send_answer(question_list[question_pos].article_id)
        }

        function send_answer(article_id) {
            var quote_content = document.getElementById('quote_content').innerText
            var quote_origin = document.getElementById('quote_origin').innerText
            var quote_actual = document.getElementById('quote_actual').value
            var quote_pos = null
            var radio_btns = document.getElementsByName('optradio')
            for(var i = 0; i < radio_btns.length; i++) {
                if(radio_btns[i].checked) {
                    quote_pos = i.toString()
                    break
                }
            }
            if(quote_pos=='3'){
                quote_pos = null
            }
            console.log(quote_pos)
            axios.post('/api/encoders/'+encoderUuid+'/articles/'+article_id, {
                //取得表單內容並送出
                quote_content: quote_content,
                quote_origin: quote_origin,
                quote_actual: quote_actual,
                quote_pos : quote_pos
            })
            .then(function (response) {
                console.log('send_answer '.article_id + ' '+response.status)
                if(response.status == 200) {
                    question_list[question_pos]['is_answered'] = true
                    question_pos+=1
                    if (question_list >= item_sum ) {
                        current_article_id = question_list[0]['article_id']
                    } else {
                        current_article_id = question_list[question_pos]['article_id']
                    }
                    update_object(current_article_id)
                } else {
                    alert('編碼錯誤')
                }
            })
            .catch(function (error) {
                console.log(error);
            });
        }

        //擷取引述按鈕
        function pick_quote() {
            var showarea = document.getElementById('quote_content')
            var parent_id = document.getSelection()['anchorNode']['parentElement'].id
            if(parent_id == 'origin') {
                var txt = document.getSelection().toString()
                showarea.innerText = txt
            }
        }

        //擷取消息來源按鈕
        function pick_source() {
            var showarea = document.getElementById('quote_origin')
            var parent_id = document.getSelection()['anchorNode']['parentElement'].id
            if(parent_id == 'quote_content') {
                var txt = document.getSelection().toString()
                showarea.innerText = txt
            }
        }

        </script>
        <!-- axios -->
        <script src="https://unpkg.com/axios/dist/axios.min.js"></script> 
        <!-- jQuery CDN - Slim version (=without AJAX) -->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <!-- Popper.JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
        <!-- Bootstrap JS -->
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>
    </body>
</html>
