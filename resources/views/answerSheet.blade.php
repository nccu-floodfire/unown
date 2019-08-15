<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <title>EncodePage</title>
        <!-- Bootstrap CSS CDN -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
        <link rel="stylesheet" href="{{ url('/css/mycss.css') }}">

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
                    <hr>
                    <ul class="list-unstyled components" id='bar_items'>
                    </ul>
                </div>
                <!-- Page Content -->
                <div class="col-8"x id = 'content'>
                    <div class="form-group">
                        <label class='mylabels'>・原始文章</label>
                        <p class="text-left conetent_area" id="origin"></p>
                        <div class='btn_area'><button type="button" id='extract_btn1' onclick = "pick_quote()" class="btn btn-primary">擷取引述</button></div>
                        <label class='mylabels'>・報導引述內容</label>
                        <p class="text-left conetent_area" id="quote_content"></p>
                        <div class='btn_area'><button type="button" id='extract_btn2' onclick = "pick_source()" class="btn btn-primary">擷取消息來源</button></div>
                        <label class='mylabels'>・消息來源名稱</label>
                        <p class="text-left conetent_area" id="quote_origin"></p>
                        <label class='mylabels'>・文章內首次出現名稱</label>
                        <textarea class="form-control conetent_area" rows="1" id="quote_actual"></textarea>
                        <label class='mylabels'>・引述對象位置</label>
                        <br>
                        <label class="radio-inline"><input type="radio" name="optradio" value=0><span style='margin-left:5pt;'>0（切分單位前）</span></label>
                        <label class="radio-inline"><input type="radio" name="optradio" value=1 checked><span style='margin-left:5pt;'>1（切分單位中）</span></label>
                        <label class="radio-inline"><input type="radio" name="optradio" value=2><span style='margin-left:5pt;'>2（切分單位後）</span></label>
                        <label class="radio-inline"><input type="radio" name="optradio" value=3><span style='margin-left:5pt;'>無</span></label>
                    </div>
                    <div class='btn_area'>
                    <button type="button" onclick = "send_result()" class="btn btn-primary" id = 'new_btn'>新增結果</button>
                    <button type="button" onclick = "update_result()" class="btn btn-primary" id = 'update_btn' style='display:none;'>修改結果</button>
                    <button type="button" onclick = "delete_result()" class="btn btn-danger" id = 'delete_btn' style='display:none;'>刪除結果</button>
                    </div>
                </div>
                <div class="col-2" id = 'function_part'>
                    <div id="function_search">
                        <label class='mylabels'>標記定位</label>
                        <textarea class="form-control conetent_area" rows="1" id="search_inside" placeholder="輸入標記文字"></textarea>
                        <div class='btn_area'>
                            <button type="button" class="btn btn-danger" onclick="search_key()">標記</button>
                            <button type="button" class="btn btn-danger" onclick="clear_search()">清除標記</button>
                        </div>
                    </div>
                    <hr>
                    <div id="function_results">
                        <div id='current_result' class='mylabels'>已有作答：</div>
                        <div id="result_buttons"></div>
                    </div>
                </div>
            </div>
        </div>       

       <script> 
        // 原始路徑
        var baseURL = "{!! url('/') !!}";
        // 該編碼者代碼
        var encoderUuid = {!!json_encode($encoderUuid) !!};
        // 要回答的問題集
        var question_list = {!! json_encode($question_list) !!};
        // 當前問題是問題集的哪一個位置
        var question_pos = -1
        // 問題總數
        var item_sum
        // 當前問題內容
        var current_item = null
        // 當前回應是回應集的哪一個位置
        var result_idx = -1
        var quote_index_pos = [-1, -1, -1, -1]

        window.onload = function () {
            var items = document.getElementById('bar_items')
            var item_sum = question_list.length
            for( var i=0; i<item_sum; i++) {
                question = question_list[i]
                var tmplist = document.createElement("li");
                tmplist.innerText = question['article_id']+ '(' + question['answer_time'] +')'
                tmplist.value = i
                tmplist.setAttribute('onclick','switch_object(this);'); // for FF
                tmplist.onclick = function() {switch_object(this);}; // for IE
                tmplist.classList.add('question_btn')
                if(question['is_answered']) {
                    tmplist.classList.add('has_answered')
                } else {
                    tmplist.classList.add('not_answered')
                    // 當前尚未回答過的第一題
                    if (question_pos < 0) {
                        question_pos = i
                        tmplist.classList.add('answering')
                    }
                }
                items.appendChild(tmplist)
            }
            // 如果都回答過了，則回到第一題
            if (question_pos < 0) {
                question_pos = 0
            }
            // 載入題目
            document.getElementById('origin').scrollTop = 0;
            current_article_id = question_list[question_pos]['article_id']
            update_object(current_article_id)
        }
        
        //按下題目按鈕，修改題目
        function switch_object(obj) {
            var current_btn = document.getElementsByClassName('question_btn')[question_pos]
            current_btn.classList.remove('answering')
            question_pos = obj.value
            current_btn = document.getElementsByClassName('question_btn')[question_pos]
            current_btn.classList.add('answering')
            current_article_id = question_list[question_pos]['article_id']
            //新的一題，要滾回最上面
            document.getElementById('origin').scrollTop = 0;
            update_object(current_article_id)
        }

        //更新表單內容
        function update_object(article_id) {
            axios.get(baseURL+'/api/encoders/'+encoderUuid+'/articles/'+article_id)
            .then(function (response) {
                console.log('load_object '+article_id + ' '+response.status)
                if(response.status == 200) {
                    // 填入內容
                    current_item = response.data
                    document.getElementById('origin').innerText = current_item.article.body
                    document.getElementById('quote_content').innerText = ''
                    document.getElementById('quote_origin').innerText = ''
                    document.getElementById('quote_actual').value = ''
                    document.getElementsByName('optradio')['1'].checked = true
                    quote_index_pos = [-1, -1, -1, -1]
                    // 清空回應列+回應指標歸-1
                    var button_area = document.getElementById('result_buttons')
                    while (button_area.firstChild) {
                        button_area.firstChild.remove()
                    }
                    document.getElementById('update_btn').style.display = 'none'
                    document.getElementById('delete_btn').style.display = 'none'
                    result_idx = -1
                    // 清空搜尋欄
                    document.getElementById('search_inside').value = ''

                    if(current_item.result.length > 0) {
                        // 如果該題已經有回答過，則產生回答列表按鈕
                        create_result_button()
                    }
                } else {
                    alert('載入題目錯誤')
                }
            })
            .catch(function (error) {
                console.log(error);
            });
        }

        //創建回答按鈕
        function create_result_button() {
            var button_area = document.getElementById('result_buttons');
            var results = current_item.result
            // 創建清空按鈕，以0代表
            var tmpbtn = document.createElement("button");
            tmpbtn.type = 'button'
            tmpbtn.value = 0
            tmpbtn.classList.add('btn')
            tmpbtn.classList.add('btn-dark')
            tmpbtn.classList.add('clean-answer-btn')
            tmpbtn.innerText = '清空編碼欄'
            tmpbtn.setAttribute('onclick','fill_the_result(this);'); // for FF
            tmpbtn.onclick = function() {fill_the_result(this);}; // for IE
            button_area.appendChild(tmpbtn)
            button_area.appendChild(document.createElement("br"))
            // 依序創建已回應的回應按鈕，以1開始
            for (var i = 0; i < results.length; i++) {
                var tmpbtn = document.createElement("button");
                tmpbtn.type = 'button'
                tmpbtn.value = i+1
                tmpbtn.classList.add('btn')
                tmpbtn.classList.add('btn-outline-dark')
                tmpbtn.classList.add('answer_btn')
                tmpbtn.innerText = i+1
                tmpbtn.setAttribute('onclick','fill_the_result(this);'); // for FF
                tmpbtn.onclick = function() {fill_the_result(this);}; // for IE
                button_area.appendChild(tmpbtn)
            }
        }

        //根據按鈕替換當前的回答內容
        function fill_the_result(result_btn){
            var answer_btn = document.getElementsByClassName('answer_btn')
            if(result_idx>0) {
                answer_btn[result_idx-1].classList.remove('btn-dark')
                answer_btn[result_idx-1].classList.add('btn-outline-dark')
            }
            result_idx = result_btn.value
            if(result_idx == 0){
                //  清空按鈕
                document.getElementById('update_btn').style.display = 'none'
                document.getElementById('delete_btn').style.display = 'none'
                document.getElementById('current_result').innerText = '已有作答：'
                document.getElementById('quote_content').innerText = ''
                document.getElementById('quote_origin').innerText = ''
                document.getElementById('quote_actual').value = ''
                document.getElementsByName('optradio')['1'].checked = true
                quote_index_pos = [-1, -1, -1, -1]
            } else {
                //  匯入回應內容
                document.getElementById('update_btn').style.display = ''
                document.getElementById('delete_btn').style.display = ''
                document.getElementById('current_result').innerText = '已有作答：(當前匯入'+result_idx+'）'
                var result = current_item.result[result_idx-1] // 因為從1開始，故需要減少1才會是array的位置
                document.getElementById('quote_content').innerText = result['quote_content']
                document.getElementById('quote_origin').innerText = result['quote_origin']
                document.getElementById('quote_actual').value = result['quote_actual']
                quote_index_pos = result['note']
                if (quote_index_pos == null) {
                    quote_index_pos = [-1, -1, -1, -1]
                }
                if (result['quote_pos'] == 3 || result['quote_pos'] == null) {
                    document.getElementsByName('optradio')['3'].checked = true
                } else {
                    document.getElementsByName('optradio')[result['quote_pos']].checked = true
                }
                answer_btn[result_idx-1].classList.add('btn-dark')
                answer_btn[result_idx-1].classList.remove('btn-outline-dark')
            }
        }

        //新增一筆結果
        function send_result(){
            send_answer(question_list[question_pos].article_id, null)
        }

        //更新當前結果
        function update_result(){
            send_answer(question_list[question_pos].article_id, current_item.result[result_idx-1].id)
        }

        //刪除一個結果
        function delete_result(){
            result_id = current_item.result[result_idx-1].id
            axios.delete(baseURL+'/api/encoders/'+encoderUuid+'/result/'+result_id, {
            })
            .then(function (response) {
                console.log('delete_answer '.result_id + ' '+response.status)
                if(response.status == 200) {
                    // 回答次數-1，更改內置文字，如果歸零，當前的按鈕要改為"未被回答過"
                    var current_btn = document.getElementsByClassName('question_btn')[question_pos]
                    var current_qlist = question_list[question_pos]
                    current_qlist.answer_time-=1
                    current_btn.innerText = current_qlist['article_id']+ '(' + current_qlist['answer_time'] +')'
                    if (current_qlist.answer_time ==0) {
                        current_qlist['is_answered'] = false
                    }
                    // 更新題目內容
                    update_object(current_article_id)
                } else {
                    alert('刪除錯誤')
                }
            })
            .catch(function (error) {
                console.log(error);
            });
        }

        //新增or修改一筆結果
        function send_answer(article_id, result_id) {
            if (quote_index_pos[2]<quote_index_pos[0] || quote_index_pos[3]>quote_index_pos[1]){
                if (quote_index_pos[2]!=-1 || quote_index_pos[3]!=-1){
                    alert('消息來源不在報導引述內，請再確認一下')
                    return false
                }
            }
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

            axios.post(baseURL+'/api/encoders/'+encoderUuid+'/articles/'+article_id, {
                //取得表單內容並送出
                quote_content: quote_content,
                quote_origin: quote_origin,
                quote_actual: quote_actual,
                quote_pos : quote_pos,
                result_id : result_id,
                note : quote_index_pos
            })
            .then(function (response) {
                console.log('send_answer '.article_id + ' '+response.status)
                if(response.status == 200) {
                    // 如果是新增的情形，當前的按鈕要改為"被回答過"，回答次數+1，更改內置文字
                    if (result_id == null) {
                        var current_btn = document.getElementsByClassName('question_btn')[question_pos]
                        var current_qlist = question_list[question_pos]
                        current_qlist['is_answered'] = true
                        current_qlist.answer_time+=1
                        current_btn.innerText = current_qlist['article_id']+ '(' + current_qlist['answer_time'] +')'
                    }
                    // 更新題目內容
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
            target_parent_id = 'origin'
            if(parent_id == target_parent_id || parent_id == '') {
                var all_text = document.getElementById(target_parent_id).innerText
                //段落開頭
                var pstart = all_text.indexOf(window.getSelection().baseNode.data)
                //段落offset
                var poffset = window.getSelection().baseOffset
                //開始位置
                var start_pos = pstart+poffset
                //選取長度
                var select_length = window.getSelection().toString().length
                //結束位置
                var end_pos = start_pos + select_length
                //重新確認取得文字
                var checked_pick_content = document.getElementById(target_parent_id).innerText.substring(start_pos, end_pos)
                //重新確認結束位置(避免超出選取)
                var new_end_pos = start_pos + checked_pick_content.length
                //最終確認取得文字
                var final_content = document.getElementById(target_parent_id).innerText.substring(start_pos, new_end_pos)                
                showarea.innerText = final_content
                if (final_content.length > 0) {
                    quote_index_pos[0] = start_pos
                    quote_index_pos[1] = new_end_pos
                }
            }
        }

        //擷取消息來源按鈕
        function pick_source() {
            var showarea = document.getElementById('quote_origin')
            var parent_id = document.getSelection()['anchorNode']['parentElement'].id
            target_parent_id = 'quote_content'
            if(parent_id == target_parent_id) {
                var all_text = document.getElementById(target_parent_id).innerText
                //段落開頭
                var pstart = all_text.indexOf(window.getSelection().baseNode.data)
                //段落offset
                var poffset = window.getSelection().baseOffset
                //開始位置
                var start_pos = pstart+poffset
                //選取長度
                var select_length = window.getSelection().toString().length
                //結束位置
                var end_pos = start_pos + select_length
                //重新確認取得文字
                var checked_pick_content = document.getElementById(target_parent_id).innerText.substring(start_pos, end_pos)
                //重新確認結束位置(避免超出選取)
                var new_end_pos = start_pos + checked_pick_content.length
                //最終確認取得文字
                var final_content = document.getElementById(target_parent_id).innerText.substring(start_pos, new_end_pos)
                showarea.innerText = final_content
                if (final_content.length > 0) {
                    quote_index_pos[2] = quote_index_pos[0]+start_pos
                    quote_index_pos[3] = quote_index_pos[0]+new_end_pos
                }
            }
        }

        function search_key() {
            clear_search()
            var text_area = document.getElementById('origin')
            feature = document.getElementById('search_inside').value
            if (feature!=''){
                text_area.innerHTML = text_area.innerHTML.replace(RegExp(feature,"g"), 
                                                              '<span class="spark">'+feature+'</span>')
            }
        }

        function clear_search() {
            var text_area = document.getElementById('origin')
            text_area.innerText = current_item.article.body
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
