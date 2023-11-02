function cObj(id) {
    return document.getElementById(id);
}
function valObj(id) {
    return document.getElementById(id).value;
}

window.onload = function () {
    var topics = document.getElementsByClassName("topics");

    for (let index = 0; index < topics.length; index++) {
        const element = topics[index];
        element.onchange = function () {
            displayData(router_logs_sorted_by_day);
        }
    }

    displayData(router_logs_sorted_by_day);
    
}

function displayData(data) {
    // check for filters
    var searchkey = valObj("searchkey");
    var date_selector = valObj("date_selected_data");
    // console.log(date_selector);
    date_selector = date_selector.replace(/-/g, "");
    var date_selected_data = valObj("date_selector");
    var topics = document.getElementsByClassName("topics");
    // get the topics to be excepmted
    var topics_to_include = [];
    for (let index = 0; index < topics.length; index++) {
        const element = topics[index];
        if (element.checked == true) {
            topics_to_include.push(element.value);
        }
    }
    var count = 0;
    // console.log(topics_to_include);

    // var display_data = [];
    var data_to_display = "<table class='table'><thead class='thead-dark'><tr><th>#</th><th>Log Time</th><th>Topics</th><th>Message</th></tr></thead><tbody >";
    if (date_selected_data == "All") {
        for (let index = (data.length-1); index >= 0; index--) {
            const element = data[index];
            var daily_data = element.daily_data;
            var date_long = element.date_long;
            var date = element.date;

            data_to_display+="<tr><td colspan='4' class='text-secondary'><b>"+date+"</b></td></tr>"
            var counter = 1;
            for (let index2 = 0; index2 < daily_data.length; index2++) {
                const elem = daily_data[index2];
                var time = elem.time;
                var topic = elem.topics;
                var message = elem.message;

                // if present
                var present = 0;
                var topic_str = "";
                // check if the topic is present
                for (let i = (topic.length-1); i >= 0; i--) {
                    const element = topic[i];
                    if (isPresent(topics_to_include,topic[i])) {
                        present = 1;
                    }
                    topic_str+=element+",";
                }
                topic_str = topic_str.substring(0,(topic_str.length-1));

                // if the topic is present check if the search keyword is contained in the date and message
                if (present > 0) {
                    if (message.toString().toLowerCase().includes(searchkey.toString().toLowerCase()) || time.toString().toLowerCase().includes(searchkey)) {
                        present = 1;
                    }else{
                        present = 0;
                    }
                }

                // if present add it to data to display
                if (present>0) {
                    data_to_display+="<tr><th scope='row'>"+counter+"</th><td>"+time+"</td><td>"+topic_str+"</td><td>"+message+"</td></tr>";
                    counter++;
                    count++;
                }
            }

            if (counter == 1) {
                data_to_display+="<tr><td colspan='4' class='text-secondary text-center'><span style='font-size:40px;'><i class='ft-alert-triangle'></i></span> <br>Ooops! No results found for "+date+"!</td></tr>"
            }
        }
    }else{
        for (let index = (data.length-1); index >= 0; index--) {
            const element = data[index];
            var daily_data = element.daily_data;
            var date_long = element.date_long;
            var date = element.date;
            // console.log(date_long+" "+date_selector);

            if (date_long == date_selector) {
                data_to_display+="<tr><td colspan='4' class='text-secondary'><b>"+date+"</b></td></tr>"
                var counter = 1;
                for (let index2 = 0; index2 < daily_data.length; index2++) {
                    const elem = daily_data[index2];
                    var time = elem.time;
                    var topic = elem.topics;
                    var message = elem.message;
    
                    // if present
                    var present = 0;
                    var topic_str = "";
                    // check if the topic is present
                    for (let i = (topic.length-1); i >= 0; i--) {
                        const element = topic[i];
                        if (isPresent(topics_to_include,topic[i])) {
                            present = 1;
                        }
                        topic_str+=element+",";
                    }
                    topic_str = topic_str.substring(0,(topic_str.length-1));
    
                    // if the topic is present check if the search keyword is contained in the date and message
                    if (present > 0) {
                        if (message.toString().toLowerCase().includes(searchkey.toString().toLowerCase()) || time.toString().toLowerCase().includes(searchkey)) {
                            present = 1;
                        }else{
                            present = 0;
                        }
                    }
    
                    // if present add it to data to display
                    if (present>0) {
                        count++;
                        data_to_display+="<tr><th scope='row'>"+counter+"</th><td>"+time+"</td><td>"+topic_str+"</td><td>"+message+"</td></tr>";
                        counter++;
                    }
                }
                if (counter == 1) {
                    data_to_display+="<tr><td colspan='4' class='text-secondary text-center'><span style='font-size:40px;'><i class='ft-alert-triangle'></i></span> <br>Ooops! No results found for "+date+"!</td></tr>"
                }
            }
        }
    }
    data_to_display+="</tbody></table>";

    if (count > 0) {
        cObj("transDataReciever").innerHTML = data_to_display;
    }else{
        cObj("transDataReciever").innerHTML = "<p class='sm-text text-danger text-bold text-center'><span style='font-size:40px;'><i class='ft-alert-triangle'></i></span> <br>Ooops! No results found!</p>";
    }
}

cObj("searchkey").onkeyup = function () {
    displayData(router_logs_sorted_by_day);
}

function isPresent(array,string) {
    for (let index = 0; index < array.length; index++) {
        const element = array[index];
        if (element == string) {
            return true;
        }
    }
    return false;
}

cObj("date_selector").onchange = function () {
    if (this.value == "select_date") {
        cObj("date_selector_window").classList.remove("invisible");
        displayData(router_logs_sorted_by_day);
    }else{
        cObj("date_selector_window").classList.add("invisible");
        displayData(router_logs_sorted_by_day);
    }
}

cObj("date_selected_data").onchange = function () {
    displayData(router_logs_sorted_by_day);
}