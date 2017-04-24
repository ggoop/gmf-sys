import MdAvatar from './mdAvatar';
import MdBackdrop from './mdBackdrop';
import MdBottomBar from './mdBottomBar';
import MdButton from './mdButton';
import MdButtonToggle from './mdButtonToggle';
import MdCard from './mdCard';
import MdCheckbox from './mdCheckbox';
import MdChips from './mdChips';
import MdDialog from './mdDialog';
import MdDivider from './mdDivider';
import MdFile from './mdFile';
import MdIcon from './mdIcon';
import MdImage from './mdImage';
import MdInputContainer from './mdInputContainer';
import MdLayout from './mdLayout';
import MdList from './mdList';
import MdMenu from './mdMenu';
import MdProgress from './mdProgress';
import MdRadio from './mdRadio';
import MdSelect from './mdSelect';
import MdSidenav from './mdSidenav';
import MdSnackbar from './mdSnackbar';
import MdSpeedDial from './mdSpeedDial';
import MdSpinner from './mdSpinner';
import MdSubheader from './mdSubheader';
import MdSwitch from './mdSwitch';
import MdTable from './mdTable';
import MdTabs from './mdTabs';
import MdToolbar from './mdToolbar';
import MdTooltip from './mdTooltip';
import MdWhiteframe from './mdWhiteframe';
import MdEnum from './mdEnum';
import MdRef from './mdRef';
import mdLoading from './mdLoading';
import mdWrap from './mdWrap';
import mdToast from './mdToast';
import mdContent from './mdContent';
import mdQuery from './mdQuery';
import mdPart from './mdPart';
import mdTree from './mdTree';
import mdChart from './mdChart';

const options = {
    MdAvatar,
    MdBackdrop,
    MdBottomBar,
    MdButton,
    MdButtonToggle,
    MdCard,
    MdCheckbox,
    MdChips,
    MdDialog,
    MdDivider,
    MdFile,
    MdIcon,
    MdImage,
    MdInputContainer,
    MdLayout,
    MdList,
    MdMenu,
    MdProgress,
    MdRadio,
    MdSelect,
    MdSidenav,
    MdSnackbar,
    MdSpeedDial,
    MdSpinner,
    MdSubheader,
    MdSwitch,
    MdTable,
    MdTabs,
    MdToolbar,
    MdTooltip,
    MdWhiteframe,
    MdEnum,
    MdRef,
    mdLoading,
    mdWrap,
    mdToast,
    mdContent,
    mdQuery,
    mdPart,
    mdTree,
    mdChart,
};

export default function install(Vue) {
    if (install.installed) {
        console.warn('Vue components is already installed.');
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
