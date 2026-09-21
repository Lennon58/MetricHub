export function useMetricaForm(
    form,
    isEditing,
    props,
    { minutesToTime, timeToMinutes },
    { isHorasExcedidas, isNpsInvalido, hasCreatedNewMetrica } = {}
) {

    function resetForm() {
        isEditing.value = false;
        form.reset();
        form.user_id = props.isGestor ? '' : props.currentUserId;
        form.tipo = 'Meta de Vendas (R$)';
        form.clearErrors();
    }

    function editMetrica(item) {
        isEditing.value = true;
        form.clearErrors();
        form.id = item.id;
        form.user_id = props.isGestor ? (item.user_id || '') : props.currentUserId;
        form.data_inicio = item.data_inicio;
        form.data_fim = item.data_fim;
        form.tipo = item.tipo;
        form.observacao = item.observacao || '';

        if (item.valor !== null && item.valor !== undefined && item.valor !== '') {
            if (item.tipo.includes('(R$)')) {
                const num = parseFloat(item.valor) || 0;
                form.valor = num.toLocaleString('pt-BR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2,
                });
            } else if (item.tipo === 'Horas Trabalhadas') {
                form.valor = minutesToTime(item.valor);
            } else {
                form.valor = String(item.valor);
            }
        } else {
            form.valor = '';
        }
    }

    function tratarValorEnvio(tipo, valor) {
        if (tipo.includes('(R$)')) {
            if (typeof valor === 'number') return valor;
            if (!valor) return 0;
            const valorLimpo = String(valor).replace(/\./g, '').replace(',', '.');
            return parseFloat(valorLimpo) || 0;
        }
        if (tipo === 'Horas Trabalhadas') {
            return timeToMinutes(valor);
        }
        return Number(valor) || 0;
    }

    function submit() {
        if (isHorasExcedidas?.value || isNpsInvalido?.value) return;

        const payloadTransform = (data) => ({
            ...data,
            valor: tratarValorEnvio(data.tipo, data.valor),
        });

        if (isEditing.value) {
            form.transform(payloadTransform).put(route('metricas.update', form.id), {
                onSuccess: () => resetForm(),
            });
        } else {
            form.transform(payloadTransform).post(route('metricas.store'), {
                onSuccess: () => {
                    resetForm();
                    if (hasCreatedNewMetrica) hasCreatedNewMetrica.value = true;
                },
            });
        }
    }

    return { resetForm, editMetrica, tratarValorEnvio, submit };
}