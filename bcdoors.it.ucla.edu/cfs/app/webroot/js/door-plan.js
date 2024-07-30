var inputSelector = "#door-plan input[data-id]";
var checkAllSelector = '#chk-all';
var plans = null;
var currentCardHolders = null;
$(document).ready(function()
{
    $("input[type=checkbox]").prop('checked', false);
    $('#door-plan button.btn-success, .card-holder-plan-buttons button.btn-success').removeProp('disabled');
    toggleButton();

    $('.card-holder-plan-buttons .btn-danger').on('click', function()
    {
        plans = getCardHolderData(inputSelector);
        var rowCNT = plans.length;
        var html = '';

        for (var loopCNT = 0; loopCNT < rowCNT; loopCNT++)
        {
            html += plans[loopCNT].name + "<br />";
        }

        $("#mod_remove-access .modal-body p:last").html(html);
        $("#mod_remove-access").modal('show');

        return false;
    });
    $('.card-holder-plan-buttons .btn-success').on('click', function()
    {
        clearTable();
        currentCardHolders = getCurrentCardHolderData(inputSelector);

        $('#mod_assign-access .modal-footer .btn-primary').disable();
        $("#mod_assign-access").modal('show');

        return false;
    });
    //----------------------------------------------
    $('#mod_remove-access .btn-primary').on('click', function()
    {
        plans = getCardHolderData(inputSelector);
        var rowCNT = plans.length;
        var data = [];
        for (var loopCNT = 0; loopCNT < rowCNT; loopCNT++)
        {
            data.push(plans[loopCNT].id);
        }

        process({'action': 'delete', 'GrouperMembership-group_name': $("#DoorPlan-plan_id").val(), 'GrouperMembership-identifier': data.join('|')});

        return false;
    });
    $('#mod_assign-access .btn-primary').on('click', function()
    {
        plans = getDoorAssignData('#door-plan-assign tbody tr[data-id]');
        var rowCNT = plans.length;
        var data = [];
        for (var loopCNT = 0; loopCNT < rowCNT; loopCNT++)
        {
            data.push(plans[loopCNT].id);
        }

        process({'action': 'add', 'GrouperMembership-group_name': $("#DoorPlan-plan_id").val(), 'GrouperMembership-identifier': data.join('|')});

        return false;
    });
    //----------------------------------------------
    $(checkAllSelector).on("change", function()
    {
        $(inputSelector).attr('checked', true);

        if ($(this).is(':checked'))
            $('input:checkbox').not(this).prop('checked', this.checked);

        toggleButton();
    });
    $(inputSelector).on("change", function()
    {
        if (!$(this).is(':checked'))
            $(checkAllSelector).prop('checked', this.checked);

        toggleButton();
    });
    //--------------------------------------------
    $('#btn_card_holder_add').on('click', function()
    {
        if ($.isEmpty(ids['no-access']) || $.isEmpty(ids['no-access'].uid))
            $('.card-add-error-message').removeClass('hide');
        else
        {
            addToTable(ids['no-access']);

            ids['no-access'] = null;
            $('#card_holder_add').val('');
            $('.card-add-error-message').addClass('hide');
            $('#mod_assign-access .modal-footer .btn-primary').enable();
        }

        return false;
    });
    //--------------------------------------------
    $('.modal').on('show.bs.modal', centerModal);
    $(window).on("resize", function()
    {
        $('.modal:visible').each(centerModal);
    });
});
function getCurrentCardHolderData(selector)
{
    var plans = new Array();
    $(selector).each(function()
    {
        plans.push({'id': $(this).attr('data-id'), 'name': $(this).closest('tr').find('td:last').text()});
    });

    return plans;
}
function getCardHolderData(selector)
{
    var plans = new Array();
    $(selector + ':checked').each(function()
    {

        var text = $("span", $('<div></div>').html($(this).closest('tr').find('td:last').html())).remove().end().text();

        plans.push({'id': $(this).attr('data-id'), 'name': text});
    });

    return plans;
}
function getDoorAssignData(selector)
{
    var plans = new Array();
    $(selector).each(function()
    {
        plans.push({'id': $(this).attr('data-id'), 'name': $(this).closest('tr').find('td:last').text()});
    });

    return plans;
}
function removeDoorAccess(plans)
{
    var rowCNT = plans.length;

    for (var loopCNT = 0; loopCNT < rowCNT; loopCNT++)
    {
        $('input[data-id=' + plans[loopCNT] + ']').closest('tr').remove();
    }

    toggleButton();
}
function getCardHolderName(id)
{
    var cnt = plans.length;
    var value = '';

    for (var loopCNT = 0; loopCNT < cnt; loopCNT++)
    {
        if (plans[loopCNT].id == id)
        {
            value = plans[loopCNT].name;
            break;
        }
    }

    return value;
}
function assignDoorAccess(xml)
{
    var html = '';

    $(xml).find("row").each
    (
        function()
        {
            var data = $.parse.xml($(this));

            if ($('input:checkbox[data-id=' + data.uid + ']').length == 0)
            {
                data.name = getCardHolderName(data.uid);
                html += "<tr>" +
                    "<td><input data-id=\"" + data.uid + "\" value=\"" + data.uid + "\" type=\"checkbox\"></td>" +
                    "<td>" + data.name + "<span class=\"label label-warning\"><small>Pending</small></span></td>" +
                    "</tr>";
            }
        }
    );
    $('#door-plan tbody').append(html);
    $(inputSelector).off();
    $(inputSelector).on("change", function()
    {
        if (!$(this).is(':checked'))
            $(checkAllSelector).prop('checked', this.checked);

        toggleButton();
    });
}
function process_complete(data)
{
    var xml = data.response;
    var errors = new Array();
    var dialogId = '';

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
            dialogId = data.params.action == 'add' ? '#mod_assign-access' : '#mod_remove-access';
            if (data.params.action == "delete")
            {
                removeDoorAccess(data.params['GrouperMembership-identifier'].split('|'));

                //==> Move to ajax request eventually
                $(checkAllSelector).prop('checked', false);
            }
            else if (data.params.action == "add")
            {
                assignDoorAccess(xml);

                //==> Move to ajax request eventually
                clearTable();
                //$("#mod_assign-access").modal('hide');
            }
            $(dialogId).modal('hide');
        }
    }
    else
        errors.push(data.error);

    if (errors.length > 0)
    {
        //displayErrors(errors);
    }

    $.blockMessage.hide();
}
function process(data)
{
    var url = "/xml/grouper_memberships/" + data.action;
    var dialogId = data.action == 'add' ? '#mod_assign-access' : '#mod_remove-access';

    $.blockMessage.show(dialogId);
    $.request.post(url, {"data": data, "callback": process_complete});
}
function toggleButton()
{
    $('.card-holder-plan-buttons button.btn-danger').attr('disabled', $(inputSelector).is(':checked') == 0);
}
function centerModal()
{
    $(this).css('display', 'block');
    var $dialog = $(this).find(".modal-dialog");
    var offset = ($(window).height() - $dialog.height()) / 2;
    // Center modal vertically in window
    $dialog.css("margin-top", offset);
}