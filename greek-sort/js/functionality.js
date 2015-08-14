// could let the jQuery select statement be an argument
function c_select(selector, state) {
	jQuery("form " + selector + " input:checkbox").prop('checked', state);
}