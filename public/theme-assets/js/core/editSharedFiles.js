// enable tooltips every where
$(function () {
    $('[data-toggle="tooltip"]').tooltip()
});
// get an object by id 
function cObj(id) {
    return document.getElementById(id);
}

function stopInterval(id) {
    clearInterval(id);
}

function cObj(id) {
    return document.getElementById(id);
}

function stopInterval(id) {
    clearInterval(id);
}

function checkBlank(id){
    let err = 0;
    if(cObj(id).value.trim().length>0){
        if (cObj(id).value.trim()=='N/A') {
            redBorder(cObj(id));
            err++;
        }else{
            grayBorder(cObj(id));
        }
    }else{
        redBorder(cObj(id));
        err++;
    }
    return err;
}
function grayBorder(object) {
    object.style.borderColor = 'gray';
}
function redBorder(object) {
    object.style.borderColor = 'red';
}
function hasJsonStructure(str) {
    if (typeof str !== 'string') return false;
    try {
        const result = JSON.parse(str);
        const type = Object.prototype.toString.call(result);
        return type === '[object Object]'
            || type === '[object Array]';
    } catch (err) {
        return false;
    }
}

cObj("table_names").onkeyup = function () {
    var this_value = this.value;
    var table_carry_data = cObj("table_carry_data").value;

    if (hasJsonStructure(table_carry_data)) {
        table_carry_data = JSON.parse(table_carry_data);
        table_carry_data.table_name = this_value;
        cObj("table_carry_data").value = JSON.stringify(table_carry_data);
    }
    displayTable();
}

window.onload = function () {
    displayTable();
}

cObj("add_columns_table").onclick = function () {
    var err = checkBlank("column_name");
    err += checkBlank("field_type");
    if (err == 0) {
        var col_id = 1;
        var table_carry_data = cObj("table_carry_data").value;

        if (hasJsonStructure(table_carry_data)) {
            table_carry_data = JSON.parse(table_carry_data);
            console.log(table_carry_data.columns.length);
            if (table_carry_data.columns.length > 0) {
                for (let index = 0; index < table_carry_data.columns.length; index++) {
                    const element = table_carry_data.columns[index];
                    if (element.id >= col_id) {
                        col_id = element.id;
                        console.log("Id "+col_id);
                    }
                }
                col_id*=1;
                col_id+=1;
                var column_data = {id:col_id,column_name:cObj("column_name").value,default_value:cObj("column_default_value").value,field_type:cObj("field_type").value};
                table_carry_data.columns.push(column_data);
                cObj("table_carry_data").value = JSON.stringify(table_carry_data);
            }else{
                var column_data = {id:col_id,column_name:cObj("column_name").value,default_value:cObj("column_default_value").value,field_type:cObj("field_type").value};
                table_carry_data.columns.push(column_data);
                cObj("table_carry_data").value = JSON.stringify(table_carry_data);
            }
        }
        

        // column data
        cObj("column_name").value = "";
        cObj("column_default_value").value = "";
    }
    displayTable();
}

function displayTable() {
    var table_carry_data = cObj("table_carry_data").value;

    if (hasJsonStructure(table_carry_data)) {
        table_carry_data = JSON.parse(table_carry_data);
        var table_name = table_carry_data.table_name;
        var columns = table_carry_data.columns;
        
        cObj("table_name_holder").innerText = table_name;

        var data_to_display = "";
        if (columns.length > 0) {
            // loop data
            data_to_display = "<table class='table'><thead><th>#</th>";
            for (let index = 0; index < columns.length; index++) {
                const element = columns[index];
                data_to_display+="<th><span data-html='true' data-toggle='tooltip' title='Default Value: "+(element.default_value.length > 0 ? element.default_value : 'Blank')+" || Field Type: "+element.field_type+"'>"+element.column_name+"</span> <span style='cursor: pointer;' class='text-danger delete_column' id='delete_column_"+element.id+"'><i class='ft-trash'></i> Del</span> <span style='cursor: pointer;' class='text-success edit_column' id='edit_column_"+element.id+"'><i class='ft-edit'></i> Edit</span></th>";
            }
            data_to_display+="</thead>";
    
            // add rows now
            data_to_display+="<tbody><tr><th scope='row'>1</th>";
            for (let index = 0; index < columns.length; index++) {
                const element = columns[index];
                data_to_display+="<td>Column "+(index+1)+" Data.</td>";
            }
            data_to_display+="</tr>";
    
            data_to_display+="<tr><th scope='row'>2</th>";
            for (let index = 0; index < columns.length; index++) {
                const element = columns[index];
                data_to_display+="<td>Column "+(index+1)+" Data.</td>";
            }
            data_to_display+="</tr></tbody></table>";
            cObj("tablefooter").classList.remove("hide");
        }else{
            cObj("tablefooter").classList.add("hide");
            data_to_display = "<h6 class='text-secondary text-center'><span style='font-size: 36px;'><i class='ft-alert-triangle'></i></span><br> Define your columns then your table will appear here!</h6>";
        }

        // data to display
        cObj("transDataReciever").innerHTML = data_to_display;

        var delete_column = document.getElementsByClassName("delete_column");
        for (let index = 0; index < delete_column.length; index++) {
            const components = delete_column[index];
            components.addEventListener("click",function () {
                var id = this.id.substr(14);
                var table_carry_data = cObj("table_carry_data").value;
                var column_data = "";
                if (hasJsonStructure(table_carry_data)) {
                    table_carry_data = JSON.parse(table_carry_data);
                    var columns = table_carry_data.columns;
                    for (let index = 0; index < columns.length; index++) {
                        const elems = columns[index];
                        if (elems.id == id) {
                            column_data = elems.column_name;
                        }
                    }
                    cObj("column_names_id").innerText = column_data;
                    cObj("delete_columns_ids").value = id;
                }
                if (cObj("delete_column_details").classList.contains("hide")) {
                    cObj("delete_column_details").classList.remove("hide");
                    cObj("delete_column_details").classList.add("show");
                    cObj("delete_column_details").classList.add("showBlock");
                }
            });
        }
        // tool tips display
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        });

        var edit_column = document.getElementsByClassName("edit_column");
        for (let index = 0; index < edit_column.length; index++) {
            const element = edit_column[index];
            element.addEventListener("click",function () {
                // get the column data
                var its_ids = this.id.substr(12);
                var table_carry_data = cObj("table_carry_data").value;
                var column_data = [];
                if (hasJsonStructure(table_carry_data)) {
                    table_carry_data = JSON.parse(table_carry_data);
                    var columns = table_carry_data.columns;
                    for (let index = 0; index < columns.length; index++) {
                        const elems = columns[index];
                        if (elems.id == its_ids) {
                            column_data = elems;
                        }
                    }
                    
                    // change data
                    cObj("column_name_change").value = column_data.column_name;
                    cObj("editted_columns").innerText = column_data.column_name;
                    cObj("column_default_value_change").value = column_data.default_value;
                    cObj("store_column_ids").value = column_data.id;
                    console.log(column_data.column_name);

                    var opts = document.getElementsByClassName("opts");
                    for (let index = 0; index < opts.length; index++) {
                        const element = opts[index];
                        if (element.value == column_data.field_type) {
                            element.selected = true;
                        }
                    }
                }
                if (cObj("edit_column_details").classList.contains("hide")) {
                    cObj("edit_column_details").classList.remove("hide");
                    cObj("edit_column_details").classList.add("show");
                    cObj("edit_column_details").classList.add("showBlock");
                }else{
                }
            });
        }
    }else{
        cObj("transDataReciever").innerHTML = "<h6 class='text-secondary text-center'><span style='font-size: 36px;'><i class='ft-alert-triangle'></i></span><br> Start By defining your columns your table sample will appear here!</h6>";
    }
}

cObj("hide_delete_column").onclick = function () {
    cObj("close_this_window_delete").click();
}
cObj("close_this_window_delete").onclick = function () {
    cObj("delete_column_details").classList.add("hide");
    cObj("delete_column_details").classList.remove("show");
    cObj("delete_column_details").classList.remove("showBlock");
}
cObj("close_this_window").onclick = function () {
    cObj("edit_column_details").classList.add("hide");
    cObj("edit_column_details").classList.remove("show");
    cObj("edit_column_details").classList.remove("showBlock");
}
cObj("hide_edit_schools").onclick = function () {
    cObj("close_this_window").click();
}
cObj("save_column_data").onclick = function () {
    var err = checkBlank("column_name_change");
    err+=checkBlank("change_field_type");

    if (err == 0) {
        var new_data = {id:cObj("store_column_ids").value,column_name:cObj("column_name_change").value,default_value:cObj("column_default_value_change").value,field_type:cObj("change_field_type").value};
        var table_carry_data = cObj("table_carry_data").value;
        if (hasJsonStructure(table_carry_data)){
            table_carry_data = JSON.parse(table_carry_data);

            var columns = table_carry_data.columns;
            for (let index = 0; index < columns.length; index++) {
                const element = columns[index];
                if (element.id == new_data.id) {
                    table_carry_data.columns[index] = new_data;
                }
            }
            cObj("table_carry_data").value = JSON.stringify(table_carry_data);
            
        }
    }
    // display table
    displayTable();
    cObj("edit_column_details").classList.add("hide");
    cObj("edit_column_details").classList.remove("show");
    cObj("edit_column_details").classList.remove("showBlock");
}
cObj("yes_delete_column_data").onclick = function () {
    cObj("close_this_window_delete").click();
    var my_id = cObj("delete_columns_ids").value;
    var table_carry_data = cObj("table_carry_data").value;
    if (hasJsonStructure(table_carry_data)) {
        table_carry_data = JSON.parse(table_carry_data);
        var new_columns = [];
        for (let index = 0; index < table_carry_data.columns.length; index++) {
            const element = table_carry_data.columns[index];
            if (element.id == my_id) {
                continue;
            }
            new_columns.push(element);
        }

        table_carry_data.columns = new_columns;
        cObj("table_carry_data").value = JSON.stringify(table_carry_data);
        displayTable();
    }
}