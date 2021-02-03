(function ($) {
    $(document).ready(function() {
        //var $wrapper = $('.js-collection-wrapper');

        $(document).on('click', '.js-collection-item-remove', function(e) {
            e.preventDefault();

            $(this).closest('.js-collection-item')
                .remove();
        });

        $(document).on('click','.js-collection-item-add',  function(e) {

            e.preventDefault();
         //  var $wrapper = $(this).closest('.js-collection-wrapper');

           var wrapperClass=$(this).data('wrapper');




         // var $wrapper=$('.'+wrapperClass);

         var $wrapper= $(this).closest('.buttons').prev('.'+wrapperClass);

         //console.log($wrapper.html());

            // Get the data-prototype explained earlier
            var prototype = $wrapper.data('prototype');


            // get the new index
            var index = $wrapper.data('index');


            // Replace '__name__' in the prototype's HTML to
            // instead be a number based on how many items we have

            var prototypeName=$(this).data('prototype-name');
            if(prototypeName){
                var newForm= prototype.replace(new RegExp(prototypeName, 'g'), index);
            }else{
                var newForm = prototype.replace(/__name__/g, index);
            }


            // increase the index with one for the next item
            $wrapper.data('index', index+1);


            // Display the form in the page before the "new" link
            //$(this).before(newForm);
            $wrapper.append(newForm);

        });
    });
})(jQuery);