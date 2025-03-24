<script setup>
    import { ref } from 'vue';

    const accept1 = ref(false);
    const accept2 = ref(false);

    const emit = defineEmits(['close']);

    const closeModal = () => {
        if (accept1.value && accept2.value) {
            emit('close');
        }
    };
</script>

<template>
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-11/12 md:w-2/3 lg:w-1/2 max-h-[80vh] overflow-y-auto">
            <h2 class="text-2xl font-semibold text-blue-600 mb-4">INFORMATIVA PRIVACY AI SENSI DEL REGOLAMENTO (UE) 2016/679 (GDPR)</h2>
            
            <p class="text-gray-700 text-sm mb-4">
                CON RELATIVO CONSENSO
                In ottemperanza a quanto previsto dalla normativa in oggetto, Federico Germani, help@travelrecord.it, in qualità di "titolare del trattamento" dei dati che La/Vi riguardano, La/Vi informa che tale trattamento sarà improntato ai principi di correttezza, liceità, trasparenza e tutela della Sua/Vostra riservatezza e dei Suoi/Vostri diritti.
            </p>

            <h3 class="text-lg font-semibold text-gray-800 mt-4">Finalità del trattamento.</h3>
            <p class="text-gray-700 text-sm mb-3">
                Il trattamento dei Suoi/Vostri dati personali, ivi compresi i dati sensibili come le immagini personali, avverrà per le seguenti finalità:
                <br>1. Gestione dell’account personale e utilizzo delle funzionalità del software.
                <br>2. Archiviazione e organizzazione delle immagini caricate dall’utente per documentare i propri viaggi.
                <br>3. Visualizzazione e gestione delle immagini all’interno del proprio account personale.
                <br>4. Supporto tecnico: le immagini saranno accessibili solo in caso di richiesta di assistenza da parte dell’utente o per la risoluzione di problemi tecnici legati all’account.
                <br>5. Sicurezza e protezione dei dati: il trattamento avverrà nel rispetto delle misure di sicurezza adeguate per prevenire accessi non autorizzati o utilizzi impropri.
            </p>

            <h3 class="text-lg font-semibold text-gray-800 mt-4">Base giuridica del trattamento.</h3>
            <p class="text-gray-700 text-sm mb-3">
                • Il trattamento dei dati personali si basa sul consenso espresso dall’utente (art. 6, par. 1, lett. a GDPR).
                <br>• L’utente può revocare il consenso in qualsiasi momento senza pregiudicare la liceità del trattamento effettuato prima della revoca.
            </p>

            <h3 class="text-lg font-semibold text-gray-800 mt-4">Modalità del trattamento.</h3>
            <p class="text-gray-700 text-sm mb-3">
                Il trattamento sarà effettuato ad opera di soggetti appositamente incaricati, con strumenti elettronici e non elettronici, nel rispetto della riservatezza e della sicurezza dei dati. I Suoi/Vostri dati personali non saranno soggetti a diffusione né a processi decisionali automatizzati, ivi compresa la profilazione.
            </p>

            <h3 class="text-lg font-semibold text-gray-800 mt-4">Comunicazione dei dati.</h3>
            <p class="text-gray-700 text-sm mb-3">
                I Suoi/Vostri dati potranno essere comunicati, per le finalità sopra indicate, a:
                <br>• Collaboratori interni ed esterni autorizzati al trattamento.
                <br>• Fornitori di servizi tecnici e informatici che garantiscono il funzionamento del software.
                <br>• Autorità competenti in caso di obblighi di legge.
            </p>

            <h3 class="text-lg font-semibold text-gray-800 mt-4">Trasferimento dei dati all’estero.</h3>
            <p class="text-gray-700 text-sm mb-3">
                I dati personali potrebbero essere trasferiti verso Paesi dell’Unione Europea e verso Paesi terzi extra-UE, nel rispetto delle disposizioni di legge applicabili e con garanzie adeguate di protezione dei dati.
            </p>

            <h3 class="text-lg font-semibold text-gray-800 mt-4">Diritti dell’interessato.</h3>
            <p class="text-gray-700 text-sm mb-3">
                L’utente ha il diritto di:
                <br>• Accedere ai propri dati personali.
                <br>• Chiedere la rettifica o la cancellazione dei dati.
                <br>• Limitare o opporsi al trattamento dei dati.
                <br>• Ottenere la portabilità dei dati.
                <br>• Revocare il consenso in qualsiasi momento.
                <br>• Presentare reclamo all’Autorità Garante per la protezione dei dati personali (<a href="https://www.garanteprivacy.it" class="text-blue-600 underline">www.garanteprivacy.it</a>).
            </p>

            <h3 class="text-lg font-semibold text-gray-800 mt-4">Accettazione tramite flag ✔️</h3>
            <p class="text-gray-700 text-sm mb-3">
                <input type="checkbox" id="accept1" v-model="accept1" class="mr-2">
                <label for="accept1">Acconsento al trattamento dei miei dati personali ai fini dell’utilizzo del software.</label>
                <br>
                <input type="checkbox" id="accept2" v-model="accept2" class="mr-2">
                <label for="accept2">Acconsento al trattamento delle immagini caricate per la creazione del diario personale di viaggio e autorizzo l’accesso solo in caso di richiesta di assistenza tecnica o per obblighi di legge.</label>
            </p>

            <button @click="closeModal" type="button" :disabled="!accept1 || !accept2"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition disabled:bg-gray-400">
                Chiudi
            </button>

        </div>
    </div>
</template>