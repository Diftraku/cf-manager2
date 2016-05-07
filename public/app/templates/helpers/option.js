define(['hbs/handlebars'], function (Handlebars) {
    addOn = function(value) {
        console.log(value, this);
        var selected = value.toLowerCase() === (this.value.toString()).toLowerCase() ? ' selected="selected"' : '';
        return '<option value="' + this.value + '"' + selected + '>' + this.label + '</option>';
    }
    Handlebars.registerHelper('option', addOn);
});