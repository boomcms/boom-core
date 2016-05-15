<select name="groups[]" multiple>
	<% for (var i in groups) { %>
		<option value="<%= groups[i].id %>"><%= groups[i].getName() %></option>
	<% } %>
</select>