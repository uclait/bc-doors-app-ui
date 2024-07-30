var ids = {'card': null, 'door': null, 'no-access': null};
var searchUrls = {'card': '/json/search/card_holder', 'door': '/json/search/door_plan'};
var tableSelector = {'card': '#card-holders', 'door': '#door-plans'};
var cachedSearchs = {'card': {}, 'door': {}};
var noResultsData = [{"value": "There are no results", "id": "", "plan_id": "", "type": "door"}];

$(document).ready(function()
{
    $.xhrPool = [];
    $.xhrPool.abortAll = function() {
        $(this).each(function(idx, jqXHR) {
            jqXHR.abort();
        });
    };
    $.ajaxSetup({
        beforeSend: function(jqXHR) {
            $.xhrPool.push(jqXHR);
        }
    });
    $(document).ajaxComplete(function() {
        $.xhrPool.pop();
    });

    if ($('#card_holder').length > 0)
    {
        $("#card_holder").bind("keyup", function() 
        {
            var data = {'Search-type': 'card',
                        'Search-query': $(this).val(),
                        'Search-user_id': $('#user_id').val()};

            if ($.trim(data['Search-query']) == '')
                drawCardHolderTable(tableSelector[data['Search-type']], noResultsData);
            else if ($.isDefined(cachedSearchs[data['Search-type']][data['Search-query']]))
                drawCardHolderTable(tableSelector[data['Search-type']], cachedSearchs[data['Search-type']][data['Search-query']]);
            else
                retrieve(searchUrls.card, data);
        });
        $('#btn_card_holder').on('click', function()
        {
            return false;
            $(this).closest('form').find('#uid').val(ids.card);
            if (ids.card == '')
                $('.card-error-message').removeClass('hide');
            else
                $('.card-error-message').addClass('hide');

            return $('.card-error-message').hasClass('hide');
        });
    }
    //--------------------------------------------
    if ($('#door_plan').length > 0)
    {
        $("#door_plan").bind("keyup", function() 
        {
            var data = {'Search-type': 'door',
                        'Search-query': $(this).val(),
                        'Search-user_id': $('#user_id').val()};

            if ($.trim(data['Search-query']) == '')
                drawDoorPlanTable(tableSelector[data['Search-type']], noResultsData);
            else if ($.isDefined(cachedSearchs[data['Search-type']][data['Search-query']]))
                drawDoorPlanTable(tableSelector[data['Search-type']], cachedSearchs[data['Search-type']][data['Search-query']])
            else
                retrieve(searchUrls.door, data);
        });
        $('#btn_door_plan').on('click', function()
        {
            return false;
            $(this).closest('form').find('#uid').val(ids.card);
            if (ids.card == '')
                $('.card-error-message').removeClass('hide');
            else
                $('.card-error-message').addClass('hide');

            return $('.card-error-message').hasClass('hide');
        });
    }
    //--------------------------------------------
    if ($('#card_holder_add').length > 0)
    {
        $('#mod_assign-access .modal-footer .btn-primary').disable();
        $('#card_holder_add').typeahead(
        {
            name: 'search-no-access',
            remote: 
            {
                url: searchUrls.card + '?Search-type=no-access&Search-query=%QUERY&Search-plan_id=' + encodeURIComponent($('#DoorPlan-id').val()) + '&Search-user_id=' + encodeURIComponent($('#user_id').val()),
                replace: function ()
                {
                    var query = searchUrls.card + '?Search-type=no-access&Search-query=' + encodeURIComponent($('#card_holder_add').val()) + '&Search-plan_id=' + encodeURIComponent($('#DoorPlan-id').val()) + '&Search-user_id=' + encodeURIComponent($('#user_id').val());

                    return query;
                }
            },
            displayKey: 'value',
            cache: false,
            minLength: 1,
            highlight: true,
            limit: 250
        })
        .on('typeahead:selected', onSelected);
    }
});
function drawCardHolderTable(selector, data)
{
    var dataSet = [];
    var rowCNT = data.length;

    $(selector).removeClass('hide');
    $(selector + ' tbody.search-content tr').remove();
    for (var loopCNT = 0; loopCNT < rowCNT; loopCNT++)
    {
        dataSet.push(['&nbsp;', (data[loopCNT].uid == '' ? data[loopCNT].value : '<a href="/card_holder?uid=' + data[loopCNT].uid + '">' + data[loopCNT].value + '</a>')]);
    }

    $(selector).dataTable({"info": false,
                            "ordering": false,
                            "searching": false,
                            "lengthChange": false,
                            "pageLength": 25,
                            "bDestroy": true,
                            "bAutoWidth": false,
                            "dom": '<"top"<"clear">>rt<"bottom"fp<"clear">>',
                            "data": dataSet,
                            fnDrawCallback: function(){
                                var selector = '#' + $(this).attr('id');
                                var rowsPerPage = this.fnSettings()._iDisplayLength;
                                var rowsToShow = this.fnSettings().fnRecordsDisplay();

                                hidePagination(selector, rowsToShow <= rowsPerPage || rowsPerPage == -1);
                            },
                            "scrollY": "550px",
                            "columns":
                            [
                                { "title": "" },
                                { "title": "BruinCard Holder" }
                            ]});
}
function drawDoorPlanTable(selector, data)
{
    var rowCNT = data.length;
    var dataSet = [];

    $(selector).removeClass('hide');
    $(selector + ' tbody.search-content tr').remove();
    for (var loopCNT = 0; loopCNT < rowCNT; loopCNT++)
    {
        dataSet.push(['&nbsp;', (data[loopCNT].plan_id == '' ? data[loopCNT].value : '<a href="/door_plans?plan_id=' + data[loopCNT].plan_id + '">' + data[loopCNT].value + '</a>')]);
    }

    $('#door-plans').dataTable({"info": false,
                                "ordering": false,
                                "searching": false,
                                "lengthChange": false,
                                "pageLength": 25,
                                "bDestroy": true,
                                "bAutoWidth": false,
                                "dom": '<"top"<"clear">>rt<"bottom"fp<"clear">>',
                                "data": dataSet,
                                fnDrawCallback: function(){
                                    var selector = '#' + $(this).attr('id');
                                    var rowsPerPage = this.fnSettings()._iDisplayLength;
                                    var rowsToShow = this.fnSettings().fnRecordsDisplay();

                                    hidePagination(selector, rowsToShow <= rowsPerPage || rowsPerPage == -1);
                                },
                                "scrollY": "450px",
                                "columns":
                                [
                                    { "title": "" },
                                    { "title": "BruinCard Door Access Plans" }
                                ]});
}
function hidePagination(selector, state)
{
    if (state)
        $(selector + '_paginate').hide();
    else
        $(selector + '_paginate').show();
}
function clearTable()
{
    $('#door-plan-assign tr').remove();
}
function addToTable(values)
{
    var html = '';
    
    //==> Make sure it doesn't exist before adding
    if ($('tr[data-id=' + values.uid + ']').length == 0)
    {
        html = "<tr data-id=\"" + values.uid + "\">" +
               "<td>" +
               "<td>(p) " + values.first_name + ' ' + values.last_name + "</td>" +
               "</tr>";

        $('#door-plan-assign tbody').append(html);   
    }
}
function onSelected($e, value, name) 
{
    if(value.type == 'card')
    {   
        ids.card = value.uid;
        if (ids.card == '')
            $('#card_holder').val('');
    }
    else if (value.type == 'door')
    {
        ids.door = value.plan_id;
        if (ids.door == '')
            $('#door_plan').val('');
    }
    else if (value.type == 'no-access')
    {
        ids['no-access'] = value;

        if (ids['no-access'].uid == '')
            $('#card_holder_add').val('');
    }
}
function retrieve_complete(data)
{
    var xml = data.response;
    var errors = new Array();
    
    if ($.isEmpty(data.error))
    {
            $(xml).find("error").each
            (
                function()
                {
                    if (!$.isEmpty($(this).text()))
                        errors.push($(this).text());

                    if (!status && errors.length == 0)
                        errors.push("Unknown error. Please try again.");
                }
            );
            if (errors.length == 0)
            {
                cachedSearchs[data.params['Search-type']][data.params['Search-query']] = data.response;
                if (data.params['Search-type'] == "card")
                {
                    drawCardHolderTable(tableSelector[data.params['Search-type']], data.response);
                }
                else if (data.params['Search-type'] == "door")
                {
                    drawDoorPlanTable(tableSelector[data.params['Search-type']], data.response);
                }
            }
    }
    else
            errors.push(data.error);

    if (errors.length > 0)
    {
        
    }
    $.blockMessage.hide(tableSelector[data.params['Search-type']]);
}
function retrieve(url, data)
{
    $.xhrPool.abortAll();

    if (!$.blockMessage.visible)
        $.blockMessage.show(tableSelector[data['Search-type']]);

    $.request.get(url, {"data": data, "callback": retrieve_complete});
}