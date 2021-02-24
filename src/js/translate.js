/* global qcpConfig */

export default {
	install: app => {
		app.config.globalProperties.$translate = string => {
			return typeof qcpConfig !== 'undefined' ? qcpConfig?.strings?.[string] ?? string : string;
		}

		app.config.globalProperties.$translatePlaceholders = function(string) {
			const args = Array.prototype.slice.call(arguments, 1);

			return string.replace(/{(\d+)}/g, (match, number) => {
				return typeof args[number] != 'undefined' ? args[number] : match;
			});
		}
	}
}
