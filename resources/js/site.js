import '@popperjs/core';
import { Carousel, Tab } from 'bootstrap';
import TimeAgo from 'javascript-time-ago';
import en from 'javascript-time-ago/locale/en'

// Updated x ago
TimeAgo.addDefaultLocale(en);
const timeAgo = new TimeAgo('en-US');

document.querySelectorAll('.c-switch__updated-content').forEach(function(switchLastUpdated) {
    let updatedDate = Date.parse(switchLastUpdated.dataset.updated);
    switchLastUpdated.innerHTML = 'Last updated ' + timeAgo.format(updatedDate - 1.5 * 60 * 1000, 'round');
    switchLastUpdated.title = switchLastUpdated.dataset.updated;
});