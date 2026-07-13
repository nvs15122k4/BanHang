import { z } from 'zod';

const booleanString = z
    .enum(['true', 'false', '1', '0'])
    .transform((value) => value === 'true' || value === '1');

const port = z.coerce.number().int().min(1).max(65535);

const envSchema = z
    .object({
        APP_NAME: z.string().trim().min(1),
        APP_ENV: z.enum(['local', 'production', 'staging', 'testing']),
        APP_KEY: z.string().default(''),
        APP_DEBUG: booleanString,
        APP_URL: z.url(),
        APP_PORT: port,

        DB_CONNECTION: z.literal('mysql'),
        DB_HOST: z.string().trim().min(1),
        DB_PORT: port,
        DB_FORWARD_PORT: port.optional(),
        DB_DATABASE: z.string().trim().min(1),
        DB_USERNAME: z.string().trim().min(1),
        DB_PASSWORD: z.string().min(1),
        DB_ROOT_PASSWORD: z.string().min(1),

        CACHE_STORE: z.string().trim().min(1),
        SESSION_DRIVER: z.string().trim().min(1),
        QUEUE_CONNECTION: z.string().trim().min(1),

        VITE_PORT: port,
        VITE_DEV_SERVER_URL: z.url().optional(),
    })
    .superRefine((env, ctx) => {
        if (env.APP_ENV !== 'production') {
            return;
        }

        if (!/^base64:[A-Za-z0-9+/=]{40,}$/.test(env.APP_KEY)) {
            ctx.addIssue({
                code: z.ZodIssueCode.custom,
                path: ['APP_KEY'],
                message: 'APP_KEY is required in production and should be a Laravel base64 key.',
            });
        }

        if (env.APP_DEBUG) {
            ctx.addIssue({
                code: z.ZodIssueCode.custom,
                path: ['APP_DEBUG'],
                message: 'APP_DEBUG must be false in production.',
            });
        }

        const weakPasswords = new Set(['banhang', 'password', 'root', 'secret', 'changeme']);

        for (const key of ['DB_PASSWORD', 'DB_ROOT_PASSWORD']) {
            if (weakPasswords.has(env[key])) {
                ctx.addIssue({
                    code: z.ZodIssueCode.custom,
                    path: [key],
                    message: `${key} must be changed before production deploy.`,
                });
            }
        }
    });

const result = envSchema.safeParse(process.env);

if (!result.success) {
    console.error('Invalid environment configuration:');

    for (const issue of result.error.issues) {
        const key = issue.path.join('.') || 'ENV';
        console.error(`- ${key}: ${issue.message}`);
    }

    process.exit(1);
}

console.log(`Environment configuration is valid for APP_ENV=${result.data.APP_ENV}.`);
