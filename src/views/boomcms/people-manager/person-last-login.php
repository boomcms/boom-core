<% if (person.getLastLogin()) { %>
    <time class="since" datetime='<%= person.getLastLogin() %>'></time>
<% } else { %>
    <p><?= trans('boomcms::people-manager.person.never-logged-in') ?>
<% } %>
