# mazevo-integration

I recenty needed to embed Mazevo calendar events (https://www.gomazevo.com/) on a client website. In case anyone is attempting to do that, I'm sharing my work. 

The page template is for emedding events into a wordpress template. Because I wanted a searchable/sortable/paginated display, my rendering replies on jQuery DataTable, so you'd need to add those libraries as well to control the display:
https://cdn.datatables.net/v/dt/dt-1.11.2/datatables.min.js
https://cdn.datatables.net/v/dt/dt-1.11.2/datatables.min.css

The widget does what it says in the tin. Nothing fancy. The shortcode is [mazevo-events-widget] and there's parameter that can be used to set how many events to display. For instance, [mazevo-events-widget limit "20"] would show the next 20 events..


