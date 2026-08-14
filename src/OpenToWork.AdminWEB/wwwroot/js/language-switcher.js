window.languageSwitcher = {
    getSaved: function () {
        return localStorage.getItem('opentowork-lang') || 'es';
    },
    set: function (lang) {
        localStorage.setItem('opentowork-lang', lang);
        document.documentElement.lang = lang;
    },
    init: function () {
        const saved = this.getSaved();
        document.documentElement.lang = saved;
    }
};

document.addEventListener('DOMContentLoaded', function () {
    window.languageSwitcher.init();
});
