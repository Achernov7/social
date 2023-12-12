
import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { faMessage, faEnvelope, faImage, faMusic, faUser, faClipboard, faUsers, faBug, faUserGroup, faMagnifyingGlass, faCogs, faTrash, faPen, faEye, faReply, faDownLong, faCirclePlus, faPlus, faMinus, faEdit, faWrench, faThumbsUp as faThumbsUpSolid, faBan, faCheck, faPlay, faForward, faBackward, faPause, faVolumeOff, faVolumeXmark, faRetweet, faRandom, faArrowRight, faDownload, faXmark, faArrowLeft} from '@fortawesome/free-solid-svg-icons'
import { faFaceSmileBeam, faThumbsUp} from '@fortawesome/free-regular-svg-icons'

library.add(faMessage, faEnvelope, faImage,
     faMusic, faUser, faClipboard,
      faUsers, faBug, faUserGroup,
       faMagnifyingGlass, faCogs, faTrash, faPen, faEye,
       faReply, faDownLong, faFaceSmileBeam, faCirclePlus, faPlus, faMinus, faEdit, faWrench, faThumbsUp, faThumbsUpSolid, faBan,
       faCheck, faPlay, faForward, faBackward, faPause, faVolumeOff, faVolumeXmark, faRetweet, faRandom, faArrowRight, faDownload, faXmark,
            faArrowLeft)


export default {
    name:'font-awesome-icon',
    fontAweSomeIcon: FontAwesomeIcon
}
