const core = require('@actions/core');
const exec = require('@actions/exec');

async function run() {
    try {
        // Get the Node.js version from the action inputs
        const nodeVersion = core.getInput(['node-version']);
        if (!nodeVersion || nodeVersion !== '16.x') {
            throw new Error(`Invalid or unsupported Node.js version: ${nodeVersion}`);
        }

        // Install Node.js 16.x
        await exec.exec('n', '16');
    } catch (error) {
        core.setFailed(error.message);
    }
}

run();