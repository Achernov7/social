<template>
    <div class="unselectable" style="position: relative;">
        <div v-if="showSmiles" ref="target" class="emojiWindow roll">
            <div class="text-center">
                <span @click="chooseArrayOfSmiles(1)">😀</span>
                <span> | </span>
                <span @click="chooseArrayOfSmiles(2)">👋</span>
                <span> | </span>
                <span @click="chooseArrayOfSmiles(3)">🐵</span>
                <span> | </span>
                <span @click="chooseArrayOfSmiles(4)">🍇</span>
                <span> | </span>
                <span @click="chooseArrayOfSmiles(5)">🌍</span>
                <span> | </span>
                <span @click="chooseArrayOfSmiles(6)">👕</span>
                <span> | </span>
                <span @click="chooseArrayOfSmiles(7)">🏁</span>
            </div>
            <div class="emojies">
                <span v-for="(smile,index) in ArrayOfSmiles" :key="index" @click.stop="chooseSmile(smile)" class="emoji">
                    {{ smile }}
                </span>
            </div>
        </div>
        <font-awesome-icon @click="changeShowSmiles()" icon="fa-regular fa-face-smile-beam" class="pointer" size="xl"/>
    </div>
</template>
<script>
import { ref } from 'vue'
import { onClickOutside } from '@vueuse/core'
export default {
    name: 'Emoji',

    data() {
        return {
            SmilesAndEmotions:["😀","😃", "😄", "😁", "😆", "😅", "🤣", "😂", "🙂", "🙃","😉","😊","😇","🥰","😍","🤩","😘","😗","😚","😙","😋","😛","😜","🤪","😝","🤑","🤗","🤭","🤫","🤔","🤐"
                    ,"🤨","😐","😑","😶","😏","😒","🙄","😬","🤥","😌","😔","😪","🤤","😴","😷","🤒","🤕","🤢","🤮","🤧","🥵","🥶","🥴","😵","🤯","🤠","🥳","😎","🤓","🧐","😕","😟","🙁",
                    "😮","😯","😲","😳","🥺","😦","😧","😨","😰","😥","😢","😭","😱","😖","😣","😞","😓","😩","😫","😤","😡","😠","🤬","😈","👿","💀","💩","🤡","👹","👺","👻","👽","👾","🤖",
                    "😺","😸","😹","😻","😼","😽","🙀","😿","😾","🙈","🙉","🙊","💋","💌","💘","💝","💖","💗","💓","💞","💕","💟","❣","💔","❤","🧡","💛","💚","💙","💜"	,"🖤","💯","💢","💥",
                    "💫","💦","💨"	,"🕳","💣","💬","👁️‍🗨️","🗨","🗯"	,"💭","💤"],
            People:["👋","🤚" ,"🖐","✋", "🖖" ,"👌","✌", "🤞","🤟","🤘","🤙","👈","👉","👆","🖕","👇","☝","👍"	,"👎","✊", "👊"	,"🤛","🤜", "👏","🙌","👐","🤲","🤝","🙏","✍","💅","🤳","💪","🦵","🦶","👂","👃","🧠","🦷","🦴","👀",
                    "👁","👅","👄","👶","🧒","👦","👧","🧑","👱","👨","👱‍♂️","👨‍🦰","👨‍🦱","👨‍🦳","👨‍🦲","🧔","👩","👱‍♀️","👩‍🦰"	,"👩‍🦱","👩‍🦳","👩‍🦲","🧓","👴","👵","🙍","🙍‍♂️","🙍‍♀️","🙎","🙎‍♂️","🙎‍♀️","🙅","🙅‍♂️","🙅‍♀️","🙆","🙆‍♂️","🙆‍♀️","💁","💁‍♂️","💁‍♀️","🙋","🙋‍♂️","🙋‍♀️","🙇",
                    ,"🙇‍♂️","🙇‍♀️","🤦","🤦‍♂️","🤦‍♀️","🤷","🤷‍♂️","🤷‍♀️","👨‍⚕️","👩‍⚕️","👨‍🎓","👩‍🎓","👨‍🏫","👩‍🏫","👨‍⚖️","👩‍⚖️","👨‍🌾","👩‍🌾","👨‍🍳","👩‍🍳","👨‍🔧","👩‍🔧","👨‍🏭","👩‍🏭","👨‍💼","👩‍💼","👨‍🔬","👩‍🔬","👨‍💻","👩‍💻","👨‍🎤","👩‍🎤","👨‍🎨","👩‍🎨","👨‍✈️","👩‍✈️","👨‍🚀","👩‍🚀","👨‍🚒","👩‍🚒","👮"	
                    ,"👮‍♂️","👮‍♀️","🕵","🕵️‍♂️","🕵️‍♀️","💂","👷","🤴","👳","👲","🧕","🤵","👰","🤰","🤱","👼","🎅","🤶","🦸","🦹","🧙"	,"🧙‍♂️","🧙‍♀️","🧚","🧚‍♂️","🧚‍♀️","🧛","🧛‍♂️","🧛‍♀️","🧜","🧜‍♂️","🧜‍♀️","🧝","🧝‍♂️","🧝‍♀️","🧞","🧞‍♂️","🧞‍♀️","🧟","🧟‍♂️","🧟‍♀️","💆"	
                    ,"💆‍♂️","💆‍♀️","💇","💇‍♂️","💇‍♀️","🚶","🚶‍♂️","🚶‍♀️","🏃"	,"🏃‍♂️","🏃‍♀️","💃","🕺","🕴","👯‍♀️","🧖","🧗","🧗‍♂️","🧗‍♀️","🤺","🏇","⛷","🏂","🏌","🏌️‍♂️","🏌️‍♀️","🏄","🏄‍♂️","🏄‍♀️","🚣","🚣‍♂️","🚣‍♀️","🏊","🏊‍♂️","🏊‍♀️","⛹","⛹️‍♂️","⛹️‍♀️","🏋"	,"🏋️‍♂️","🏋️‍♀️"	,"🚴","🚴‍♂️","🚴‍♀️","🚵"	
                    ,"🚵‍♂️","🚵‍♀️","🤸","🤸‍♂️","🤸‍♀️","🤼","🤼‍♂️","🤼‍♀️","🤽"	,"🤽‍♂️","🤽‍♀️","🤾","🤾‍♂️","🤾‍♀️","🤹","🤹‍♂️","🤹‍♀️","🧘"	,"🧘‍♂️","🧘‍♀️","🛀","🛌","👭","👫","👬","💏","💑"	,"👩‍❤️‍👨","👨‍❤️‍👨","👩‍❤️‍👩","👪","👨‍👩‍👦","👨‍👩‍👧","👨‍👩‍👧‍👦","👨‍👩‍👦‍👦","👨‍👩‍👧‍👧","👨‍👨‍👦","👨‍👨‍👧","👨‍👨‍👧‍👦","👨‍👨‍👦‍👦","👨‍👨‍👧‍👧","👩‍👩‍👦","👩‍👩‍👧","👩‍👩‍👧‍👦","👩‍👩‍👦‍👦","👩‍👩‍👧‍👧","👨‍👦","👨‍👦‍👦","👨‍👧","👨‍👧‍👦","👨‍👧‍👧"	,"👩‍👦","👩‍👦‍👦"	,"👩‍👧","👩‍👧‍👦"	,"👩‍👧‍👧"	,"🗣","👤","👥"],
            Animals:["🐵","🐒","🦍","🐶","🐕","🐩","🐺","🦊","🦝","🐱","🐈","🦁","🐯","🐅","🐆","🐴","🐎","🦄","🦓","🦌","🐮","🐂","🐃","🐄","🐷","🐖","🐗","🐽","🐏","🐑","🐐","🐪","🐫","🦙","🦒","🐘","🦏","🦛","🐭","🐁","🐀","🐹","🐰","🐇","🐿","🦔","🦇","🐻","🐨","🐼"
                    ,"🦘","🦡","🐾","🦃","🐔","🐓","🐣","🐤","🐥","🐦","🐧","🕊","🦅","🦆","🦢","🦉","🦚","🦜","🐸","🐊","🐢","🦎","🐍","🐲","🐉","🦕","🦖","🐳","🐋","🐬","🐟","🐠","🐡","🦈","🐙","🐚","🐌","🦋","🐛","🐜","🐝","🐞","🦗","🕷","🕸","🦂","🦟","🦠"],
            FoodAndDrink:["🍇" ,"🍈" ,"🍉" ,"🍊" ,"🍋" ,"🍌" ,"🍍" ,"🥭" ,"🍎" ,"🍏" ,"🍐" ,"🍑" ,"🍒" ,"🍓" ,"🥝" ,"🍅" ,"🥥" ,"🥑" ,"🍆" ,"🥔" ,"🥕" ,"🌽" ,"🌶" ,"🥒" ,"🥬" ,"🥦" ,"🍄" ,"🥜" ,"🌰" ,"🍞" ,"🥐" ,"🥖" ,"🥨" ,"🥯" ,"🥞" ,"🧀" ,"🍖" ,"🍗" ,"🥩" ,"🥓" ,"🍔" ,"🍟" ,"🍕" ,"🌭" ,"🥪" ,"🌮" ,"🌯" ,"🥙" ,"🥚" ,"🍳" ,"🥘" ,
                    "🍲" ,"🥣" ,"🥗" ,"🍿" ,"🧂" ,"🥫" ,"🍱" ,"🍘" ,"🍙" ,"🍚" ,"🍛" ,"🍜" ,"🍝" ,"🍠" ,"🍢" ,"🍣" ,"🍤" ,"🍥" ,"🥮" ,"🍡" ,"🥟" ,"🥠" ,"🥡" ,"🦀" ,"🦞" ,"🦐" ,"🦑" ,"🍦" ,"🍧" ,"🍨" ,"🍩" ,"🍪" ,"🎂" ,"🍰" ,"🧁" ,"🥧" ,"🍫" ,"🍬" ,"🍭" ,"🍮" ,"🍯" ,"🍼" ,"🥛" ,"🍵" ,"🍶" ,"🍾" ,"🍷" ,"🍸" ,"🍹" ,"🍺" ,"🍻" ,"🥂" ,"🥃" ,"🥤" ,"🥢" ,"🍽" ,"🍴" ,"🥄" ,"🔪" ,"🏺"],
            PlacesAndHolidays:["🌍","🌎" ,"🌏" ,"🌐" ,"🗺" ,"🗾" ,"🧭" ,"🏔" ,"⛰" ,"🌋" ,"🗻" ,"🏕" ,"🏖" ,"🏜" ,"🏝" ,"🏞" ,"🏟" ,"🏛" ,"🏗" ,"🧱" ,"🏘" ,"🏚" ,"🏠" ,"🏡" ,"🏢" ,"🏣" ,"🏤" ,"🏥" ,"🏦" ,"🏨" ,"🏩" ,"🏪" ,"🏫" ,"🏬" ,"🏭" ,"🏯" ,"🏰" ,"💒" ,"🗼" ,"🗽" ,"⛪" ,"🕌" ,"🕍" ,"⛩" ,"🕋" ,"⛲" ,"⛺" ,"🌁" ,"🌃" ,"🏙" ,"🌄" ,"🌅" ,"🌆" ,"🌇" ,"🌉" ,"♨" ,"🌌" ,"🎠" ,"🎡" ,"🎢" ,"💈" ,"🎪" ,
                    "🚂" ,"🚃" ,"🚄" ,"🚅" ,"🚆" ,"🚇" ,"🚈" ,"🚉" ,"🚊" ,"🚝" ,"🚞" ,"🚋" ,"🚌" ,"🚍" ,"🚎" ,"🚐" ,"🚑" ,"🚒" ,"🚓" ,"🚔" ,"🚕" ,"🚖" ,"🚗" ,"🚘" ,"🚙" ,"🚚" ,"🚛" ,"🚜" ,"🏎" ,"🏍" ,"🛵" ,"🚲" ,"🛴" ,"🛹" ,"🚏" ,"🛣" ,"🛤" ,"🛢" ,"⛽" ,"🚨" ,"🚥" ,"🚦" ,"🛑" ,"🚧" ,"⚓" ,"⛵" ,"🛶" ,"🚤" ,"🛳" ,"⛴" ,"🛥" ,"🚢" ,"✈" ,"🛩" ,"🛫" ,"🛬" ,"💺" ,"🚁" ,"🚟" ,"🚠" ,"🚡" ,"🛰" ,"🚀" ,
                    "🛸" ,"🛎" ,"🧳" ,"⌛" ,"⏳" ,"⌚" ,"⏰" ,"⏱" ,"⏲" ,"🕰" ,"🕛" ,"🕧" ,"🕐" ,"🕜" ,"🕑" ,"🕝" ,"🕒" ,"🕞" ,"🕓" ,"🕟" ,"🕔" ,"🕠" ,"🕕" ,"🕡" ,"🕖" ,"🕢" ,"🕗" ,"🕣" ,"🕘" ,"🕤" ,"🕙" ,"🕥" ,"🕚" ,"🕦" ,"🌑" ,"🌒" ,"🌓" ,"🌔" ,"🌕" ,"🌖" ,"🌗" ,"🌘" ,"🌙" ,"🌚" ,"🌛" ,"🌜" ,"🌡" ,"☀" ,"🌝" ,"🌞" ,"⭐" ,"🌟" ,"🌠" ,"☁" ,"⛅" ,"⛈" ,"🌤" ,"🌥" ,"🌦" ,"🌧" ,"🌨" ,"🌩" ,
                    "🌪" ,"🌫" ,"🌬" ,"🌀" ,"🌈" ,"🌂" ,"☂" ,"☔" ,"⛱" ,"⚡" ,"❄" ,"☃" ,"⛄" ,"☄" ,"🔥" ,"💧" ,"🌊",
                    ,"🎃" ,"🎄" ,"🎆" ,"🎇" ,"🧨" ,"✨" ,"🎈" ,"🎉" ,"🎊" ,"🎋" ,"🎍" ,"🎎" ,"🎏" ,"🎐" ,"🎑" ,"🧧" ,"🎀" ,"🎁" ,"🎗" ,"🎟" ,"🎫" ,"🎖" ,"🏆" ,"🏅" ,"🥇" ,"🥈" ,"🥉" ,"⚽","⚾","🥎" ,"🏀" ,"🏐" ,"🏈" ,"🏉" ,"🎾" ,"🥏" ,"🎳" ,"🏏" ,"🏑" ,"🏒" ,"🥍" ,"🏓" ,"🏸" ,"🥊" ,"🥋" ,"🥅" ,"⛳","⛸","🎣" ,"🎽" ,"🎿" ,"🛷" ,"🥌" ,"🎯" ,"🎱" ,"🔮" ,"🧿" ,"🎮" ,"🕹" ,"🎰" ,"🎲" ,"🧩" ,"🧸" ,
                    "♠","♥","♦","♣","♟","🃏" ,"🀄" ,"🎴" ,"🎭" ,"🖼" ,"🎨" ,"🧵" ,"🧶"],
            Objects: ["👓","🕶" ,"🥽" ,"🥼" ,"👔" ,"👕" ,"👖" ,"🧣" ,"🧤" ,"🧥" ,"🧦" ,"👗" ,"👘" ,"👙" ,"👚" ,"👛" ,"👜" ,"👝" ,"🛍" ,"🎒" ,"👞" ,"👟" ,"🥾" ,"🥿" ,"👠" ,"👡" ,"👢" ,"👑" ,"👒" ,"🎩" ,"🎓" ,"🧢" ,"⛑", "📿" ,"💄" ,"💍" ,"💎" ,"🔇" ,"🔈" ,"🔉" ,"🔊" ,"📢" ,"📣" ,"📯" ,"🔔" ,"🔕" ,"🎼" ,"🎵" ,"🎶" ,"🎙" ,"🎚" ,"🎛" ,"🎤" ,"🎧" ,"📻" ,"🎷" ,"🎸" ,"🎹" ,"🎺" ,"🎻" ,"🥁" ,"📱" ,"📲" 
                    ,"☎","📞" ,"📟" ,"📠" ,"🔋" ,"🔌" ,"💻" ,"🖥" ,"🖨" ,"⌨","🖱" ,"🖲" ,"💽" ,"💾" ,"💿" ,"📀" ,"🧮" ,"🎥" ,"🎞" ,"📽" ,"🎬" ,"📺" ,"📷" ,"📸" ,"📹" ,"📼" ,"🔍" ,"🔎" ,"🕯" ,"💡" ,"🔦" ,"🏮" ,"📔" ,"📕" ,"📖" ,"📗" ,"📘" ,"📙" ,"📚" ,"📓" ,"📒" ,"📃" ,"📜" ,"📄" ,"📰" ,"🗞" ,"📑" ,"🔖" ,"🏷" ,"💰" ,"💴" ,"💵" ,"💶" ,"💷" ,"💸" ,"💳" ,"🧾" ,"💹" ,"💱" ,"💲" ,"✉","📧" ,"📨" ,"📩" ,"📤" ,
                    "📥" ,"📦" ,"📫" ,"📪" ,"📬" ,"📭" ,"📮" ,"🗳" ,"✏","✒","🖋" ,"🖊" ,"🖌" ,"🖍" ,"📝" ,"💼" ,"📁" ,"📂" ,"🗂" ,"📅" ,"📆" ,"🗒" ,"🗓" ,"📇" ,"📈" ,"📉" ,"📊" ,"📋" ,"📌" ,"📍" ,"📎" ,"🖇" ,"📏" ,"📐" ,"✂","🗃" ,"🗄" ,"🗑" ,"🔒" ,"🔓" ,"🔏" ,"🔐" ,"🔑" ,"🗝" ,"🔨" ,"⛏","⚒","🛠" ,"🗡" ,"⚔","🔫" ,"🏹" ,"🛡" ,"🔧" ,"🔩" ,"⚙","🗜" ,"⚖","🔗" ,"⛓","🧰" ,"🧲" ,"⚗","🧪" ,"🧫" ,"🧬" ,"🔬" ,"🔭" 
                    ,"📡" ,"💉" ,"💊" ,"🚪" ,"🛏" ,"🛋" ,"🚽" ,"🚿" ,"🛁" ,"🧴" ,"🧷" ,"🧹" ,"🧺" ,"🧻" ,"🧼" ,"🧽" ,"🧯" ,"🛒" ,"🚬" ,"⚰","⚱","🗿"],
            SymbolsAndFlags: ["🏧" ,"🚮" ,"🚰" ,"♿" ,"🚹" ,"🚺" ,"🚻" ,"🚼" ,"🚾" ,"🛂" ,"🛃" ,"🛄" ,"🛅" ,"⚠" ,"🚸" ,"⛔" ,"🚫" ,"🚳" ,"🚭" ,"🚯" ,"🚱" ,"🚷" ,"📵" ,"🔞" ,"☢" ,"☣" ,"⬆" ,"↗" ,"➡" ,"↘" ,"⬇" ,"↙" ,"⬅" ,"↖" ,"↕" ,"↔" ,"↩" ,"↪" ,"⤴" ,"⤵" ,"🔃" ,"🔄" ,"🔙" ,"🔚" ,"🔛" ,"🔜" ,"🔝" ,"🛐" ,"⚛" ,"🕉" ,"✡" ,"☸" ,"☯" ,"✝" ,"☦" ,"☪" ,"☮" ,"🕎" ,"🔯" ,"♈" ,"♉" ,"♊" ,"♋" ,"♌" ,"♍" ,"♎" ,"♏" ,"♐" ,"♑" ,"♒" ,"♓" ,"⛎" ,
                    "🔀" ,"🔁" ,"🔂" ,"▶" ,"⏩" ,"⏭" ,"⏯" ,"◀" ,"⏪" ,"⏮" ,"🔼" ,"⏫" ,"🔽" ,"⏬" ,"⏸" ,"⏹" ,"⏺" ,"⏏" ,"🎦" ,"🔅" ,"🔆" ,"📶" ,"📳" ,"📴" ,"♀" ,"♂" ,"⚕" ,"♾" ,"♻" ,"⚜" ,"🔱" ,"📛" ,"🔰" ,"⭕" ,"✅" ,"☑" ,"✔" ,"✖" ,"❌" ,"❎" ,"➕" ,"➖" ,"➗" ,"➰" ,"➿" ,"〽" ,"✳" ,"✴" ,"❇" ,"‼" ,"⁉" ,"❓" ,"❔" ,"❕" ,"❗" ,"〰" ,"©","®","™" ,"#️⃣" ,"🔟" ,"🔠" ,"🔡" ,"🔢" ,"🔣" ,"🔤" ,"🅰" ,"🆎" ,"🅱" ,"🆑" ,"🆒" ,"🆓" ,
                    "ℹ" ,"🆔" ,"Ⓜ" ,"🆕" ,"🆖" ,"🅾" ,"🆗" ,"🅿" ,"🆘" ,"🆙" ,"🆚" ,"🈁" ,"🈂" ,"🈷" ,"🈶" ,"🈯" ,"🉐" ,"🈹" ,"🈚" ,"🈲" ,"🉑" ,"🈸" ,"🈴" ,"🈳" ,"㊗" ,"㊙" ,"🈺" ,"🈵" ,"🔴" ,"🔵" ,"⚪" ,"⚫" ,"⬜" ,"⬛" ,"◼" ,"◻" ,"◽" ,"◾" ,"▫" ,"▪" ,"🔶" ,"🔷" ,"🔸" ,"🔹" ,"🔺" ,"🔻" ,"💠" ,"🔘" ,"🔲" ,"🔳"
                    ,"🏁","🚩" ,"🎌" ,"🏴" ,"🏳" ,"🏳️‍🌈" ,"🏴‍☠️"	,"🇦🇨" ,"🇦🇩" ,"🇦🇪" ,"🇦🇫" ,"🇦🇬" ,"🇦🇮" ,"🇦🇱" ,"🇦🇲" ,"🇦🇴" ,"🇦🇶" ,"🇦🇷" ,"🇦🇸" ,"🇦🇹" ,"🇦🇺" ,"🇦🇼" ,"🇦🇽" ,"🇦🇿" ,"🇧🇦" ,"🇧🇧" ,"🇧🇩" ,"🇧🇪" ,"🇧🇫" ,"🇧🇬" ,"🇧🇭" ,"🇧🇮" ,"🇧🇯" ,"🇧🇱" ,"🇧🇲" ,"🇧🇳" ,"🇧🇴" ,"🇧🇶" ,"🇧🇷" ,"🇧🇸" ,"🇧🇹" ,"🇧🇻" ,"🇧🇼" ,"🇧🇾" ,"🇧🇿" ,"🇨🇦" ,"🇨🇨" ,"🇨🇩" ,"🇨🇫" ,"🇨🇬" ,"🇨🇭" ,"🇨🇮" ,"🇨🇰" ,"🇨🇱" ,"🇨🇲" ,"🇨🇳" ,"🇨🇴" ,"🇨🇵" ,"🇨🇷" ,"🇨🇺" ,"🇨🇻" ,"🇨🇼" ,"🇨🇽" ,"🇨🇾" ,"🇨🇿" ,"🇩🇪" ,"🇩🇬" 
                    ,"🇩🇯" ,"🇩🇰" ,"🇩🇲" ,"🇩🇴" ,"🇩🇿" ,"🇪🇦" ,"🇪🇨" ,"🇪🇪" ,"🇪🇬" ,"🇪🇭" ,"🇪🇷" ,"🇪🇸" ,"🇪🇹" ,"🇪🇺" ,"🇫🇮" ,"🇫🇯" ,"🇫🇰" ,"🇫🇲" ,"🇫🇴" ,"🇫🇷" ,"🇬🇦" ,"🇬🇧" ,"🇬🇩" ,"🇬🇪" ,"🇬🇫" ,"🇬🇬" ,"🇬🇭" ,"🇬🇮" ,"🇬🇱" ,"🇬🇲" ,"🇬🇳" ,"🇬🇵" ,"🇬🇶" ,"🇬🇷" ,"🇬🇸" ,"🇬🇹" ,"🇬🇺" ,"🇬🇼" ,"🇬🇾" ,"🇭🇰" ,"🇭🇲" ,"🇭🇳" ,"🇭🇷" ,"🇭🇹" ,"🇭🇺" ,"🇮🇨" ,"🇮🇩" ,"🇮🇪" ,"🇮🇱" ,"🇮🇲" ,"🇮🇳" ,"🇮🇴" ,"🇮🇶" ,"🇮🇷" ,"🇮🇸" ,"🇮🇹" ,"🇯🇪" ,"🇯🇲" ,"🇯🇴" ,"🇯🇵" ,"🇰🇪" ,"🇰🇬" ,"🇰🇭" ,"🇰🇮" ,"🇰🇲" ,"🇰🇳" ,"🇰🇵" 
                    ,"🇰🇷" ,"🇰🇼" ,"🇰🇾" ,"🇰🇿" ,"🇱🇦" ,"🇱🇧" ,"🇱🇨" ,"🇱🇮" ,"🇱🇰" ,"🇱🇷" ,"🇱🇸" ,"🇱🇹" ,"🇱🇺" ,"🇱🇻" ,"🇱🇾" ,"🇲🇦" ,"🇲🇨" ,"🇲🇩" ,"🇲🇪" ,"🇲🇫" ,"🇲🇬" ,"🇲🇭" ,"🇲🇰" ,"🇲🇱" ,"🇲🇲" ,"🇲🇳" ,"🇲🇴" ,"🇲🇵" ,"🇲🇶" ,"🇲🇷" ,"🇲🇸" ,"🇲🇹" ,"🇲🇺" ,"🇲🇻" ,"🇲🇼" ,"🇲🇽" ,"🇲🇾" ,"🇲🇿" ,"🇳🇦" ,"🇳🇨" ,"🇳🇪" ,"🇳🇫" ,"🇳🇬" ,"🇳🇮" ,"🇳🇱" ,"🇳🇴" ,"🇳🇵" ,"🇳🇷" ,"🇳🇺" ,"🇳🇿" ,"🇴🇲" ,"🇵🇦" ,"🇵🇪" ,"🇵🇫" ,"🇵🇬" ,"🇵🇭" ,"🇵🇰" ,"🇵🇱" ,"🇵🇲" ,"🇵🇳" ,"🇵🇷" ,"🇵🇸" ,"🇵🇹" ,"🇵🇼" ,"🇵🇾" ,"🇶🇦" ,"🇷🇪" ,
                    "🇷🇴" ,"🇷🇸" ,"🇷🇺" ,"🇷🇼" ,"🇸🇦" ,"🇸🇧" ,"🇸🇨" ,"🇸🇩" ,"🇸🇪" ,"🇸🇬" ,"🇸🇭" ,"🇸🇮" ,"🇸🇯" ,"🇸🇰" ,"🇸🇱" ,"🇸🇲" ,"🇸🇳" ,"🇸🇴" ,"🇸🇷" ,"🇸🇸" ,"🇸🇹" ,"🇸🇻" ,"🇸🇽" ,"🇸🇾" ,"🇸🇿" ,"🇹🇦" ,"🇹🇨" ,"🇹🇩" ,"🇹🇫" ,"🇹🇬" ,"🇹🇭" ,"🇹🇯" ,"🇹🇰" ,"🇹🇱" ,"🇹🇲" ,"🇹🇳" ,"🇹🇴" ,"🇹🇷" ,"🇹🇹" ,"🇹🇻" ,"🇹🇼" ,"🇹🇿" ,"🇺🇦" ,"🇺🇬" ,"🇺🇲" ,"🇺🇳" ,"🇺🇸" ,"🇺🇾" ,"🇺🇿" ,"🇻🇦" ,"🇻🇨" ,"🇻🇪" ,"🇻🇬" ,"🇻🇮" ,"🇻🇳" ,"🇻🇺" ,"🇼🇫" ,"🇼🇸" ,"🇽🇰" ,"🇾🇪" ,"🇾🇹" ,"🇿🇦" ,"🇿🇲" ,"🇿🇼",	"🏴󠁧󠁢󠁥󠁮󠁧󠁿", "🏴󠁧󠁢󠁳󠁣󠁴󠁿",	 "🏴󠁧󠁢󠁷󠁬󠁳󠁿"],        
            indexOfArray: 1
        }
    },

    computed: {
        ArrayOfSmiles(){
                switch(this.indexOfArray){
                    case 1:
                        return this.SmilesAndEmotions
                    case 2:
                        return this.People
                    case 3:
                        return this.Animals
                    case 4:
                        return this.FoodAndDrink
                    case 5:
                        return this.PlacesAndHolidays
                    case 6:
                        return this.Objects
                    case 7:
                        return this.SymbolsAndFlags
                }
            }
    },

    methods: {
        chooseSmile(smile){
            this.showSmiles = false
            this.$emit('smile', smile)
            this.smileWasclicked = false
        },

        chooseArrayOfSmiles(index){
            switch(index){
                case 1:
                    this.indexOfArray = 1
                    break
                case 2:
                    this.indexOfArray = 2
                    break
                case 3:
                    this.indexOfArray = 3
                    break
                case 4:
                    this.indexOfArray = 4
                    break
                case 5:
                    this.indexOfArray = 5
                    break
                case 6:
                    this.indexOfArray = 6
                    break
                case 7:
                    this.indexOfArray = 7
                    break
            }
        },
        changeShowSmiles(){
            this.showSmiles = !this.showSmiles
        }
    },

    setup() {
        const target = ref(null)
        const showSmiles = ref(false)
        onClickOutside(target, (e) => {
            setTimeout(() => {
                showSmiles.value = false
            });
        })

        return { target, showSmiles }
    }
}
</script>
<style scoped>

    .emoji{
        cursor:pointer;
        font-size: 1rem;
        display: inline-block;
    }
    .emojiWindow{
        position: absolute;
        margin-left: -225px;
        margin-top: -195px;
        background: linear-gradient(15deg, rgba(237,198,156,1) 0%, rgba(221,152,146,1) 47%, rgba(109,100,205,1) 100%);;
        border-radius: 8px;
        padding: 4px;
        box-shadow: 0 0 7px rgb(0, 0, 0);
        overflow-y: auto;
        overflow-x: hidden;
        width: 250px;
        max-width: 250px;
        height: 200px;
    }
</style>