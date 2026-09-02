import Alpine from 'alpinejs'

Alpine.data('mercureDemo', (config) => ({
    subscribeUrl: config.subscribeUrl,
    type: config.type,
    connected: false,
    error: '',
    publishing: false,
    messages: config.history || [],
    eventSource: null,

    connect() {
        this.disconnect()
        this.error = ''

        if (!this.subscribeUrl) {
            this.error = 'URL de subscribe nao configurada.'
            return
        }

        try {
            this.eventSource = new EventSource(this.subscribeUrl, { withCredentials: true })
            this.eventSource.addEventListener('open', () => {
                this.connected = true
            })
            this.eventSource.addEventListener(this.type, (event) => {
                this.pushMessage(event)
            })
            this.eventSource.addEventListener('error', () => {
                this.connected = false
                this.error = 'Nao foi possivel manter a conexao com o Mercure.'
            })
        } catch (error) {
            this.connected = false
            this.error = error instanceof Error ? error.message : 'Falha ao conectar no Mercure.'
        }
    },

    disconnect() {
        if (this.eventSource) {
            this.eventSource.close()
            this.eventSource = null
        }

        this.connected = false
    },

    async publish(event) {
        const form = event.currentTarget
        this.publishing = true
        this.error = ''

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: { Accept: 'application/json' },
                body: new FormData(form),
            })
            const csrfToken = response.headers.get('X-CSRF-Token')

            if (csrfToken) {
                document.querySelectorAll('input[name="csrf_token"]').forEach((input) => {
                    input.value = csrfToken
                })
            }

            if (!response.ok) {
                const data = await response.json().catch(() => null)
                throw new Error(data?.error || 'Falha ao publicar a mensagem.')
            }
        } catch (error) {
            this.error = error instanceof Error ? error.message : 'Falha ao publicar a mensagem.'
        } finally {
            this.publishing = false
        }
    },

    pushMessage(event) {
        let payload

        try {
            payload = JSON.parse(event.data)
        } catch {
            this.error = 'O Mercure enviou uma mensagem invalida.'
            return
        }

        this.messages.unshift({
            id: payload.id,
            user_id: payload.user_id,
            type: event.type,
            message: payload.message,
            created_at: payload.created_at,
        })
    },
}))
