$(document).ready(function () {

    $(document).arrive(".js-user-autocomplete", {fireOnAttributesModification: true,existing:true},function() {
        // 'this' refers to the newly created element
       // var $newElem = $(this);

        var autocompleteUrl=$(this).data('autocomplete-url');

        $(this).autocomplete({hint: false}, [

            {
                source: function(query, cb) {
                    $.ajax({
                        url: autocompleteUrl+'?query='+query
                    }).then(function(data) {
                        cb(data.users);
                    });
                },
                displayKey: function(suggestion) {
                   return (suggestion.middleName)?
                       (suggestion.firstName+' '+suggestion.middleName+' '+suggestion.lastName):
                       (suggestion.firstName+' '+suggestion.lastName);
                },
                debounce: 500 // only request every 1/2 second
            }
        ])
    });

    Arrive.unbindAllLeave();


});

