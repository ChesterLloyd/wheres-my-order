const purchase = {
    $orderItems: '.order-items',
    $orderItemsBtnAdd: '.order-items__add',
    $orderItemsBtnDelete: '.order-items__delete',

    init: function () {
        this.addBillingAddressButton();
        this.deleteBillingAddressButton();
    },

    /**
     * Adds a new item div (set of fields) to the collection.
     */
    addBillingAddressButton: function () {
        const _this = this;
        $(this.$orderItemsBtnAdd).on('click', function () {
            const items = $(_this.$orderItems);
            let counter = items.data('widget-counter') || items.children().length;

            // Create new billing address div from the prototype template
            let newItems = items.attr('data-prototype');
            newItems = newItems.replace(/__name__/g, counter);

            // Increase the counter
            counter++;
            items.data('widget-counter', counter);

            // create a new box div element and add it to the container
            const newElem = $(items.attr('data-widget-tags')).html(newItems);
            newElem.appendTo(items);
        })
    },

    /**
     * Deletes a billing address row from the collection.
     */
    deleteBillingAddressButton: function () {
        const _this = this;
        $(document).on('click', this.$orderItemsBtnDelete, function() {
            const items = $(_this.$orderItems);
            let counter = items.data('widget-counter') || items.children().length;
            this.closest('.box').remove();

            // Decrease the counter
            counter--;
            items.data('widget-counter', counter);
        })
    },
}

export default purchase;
purchase.init();
