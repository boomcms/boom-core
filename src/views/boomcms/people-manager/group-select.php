<select name="groups[]" multiple>
	<% for (var i in groups.models) { %>
		<option value="<%= groups.models[i].id %>"<%= selectedGroups.get(groups.models[i]) ? ' selected' : '' %>><%= groups.models[i].getName() %></option>
	<% } %>
</select>