
import wave from './wave';
import colors from './colors';
import title from './title';

const options = {
    wave,
    colors,
    title,
};

export default function install(Vue) {
    if (install.installed) {
        console.warn('Vue directives is already installed.');
        return;
    }
    install.installed = true;

    for (let component in options) {
        const componentInstaller = options[component];

        if (componentInstaller && component !== 'install') {
            Vue.use(componentInstaller);
        }
    }
}
